<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCustomFieldValue;
use App\Models\UserFcmToken;
use App\Services\BootstrapTableService;
use App\Services\FileService;
use App\Services\NotificationService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Throwable;
use App\Models\Notifications;
use App\Models\FeaturedItems;
use App\Models\UserPurchasedPackage;
use Illuminate\Support\Facades\Log;

class ItemController extends Controller {

    // Service Items View
    public function serviceItems() {
        ResponseService::noAnyPermissionThenRedirect(['item-list', 'item-update', 'item-delete']);
        return view('items.service_index');
    }

    // Experience Items View
    public function experienceItems() {
        ResponseService::noAnyPermissionThenRedirect(['item-list', 'item-update', 'item-delete']);
        return view('items.experience_index');
    }

    // Original index method (can be kept for backward compatibility)
    public function index() {
        ResponseService::noAnyPermissionThenRedirect(['item-list', 'item-update', 'item-delete']);
        return view('items.index');
    }

    // Data for service items
    public function serviceItemsData(Request $request) {
        return $this->getItemsData($request, 'service');
    }

    // Data for experience items
    public function experienceItemsData(Request $request) {
        return $this->getItemsData($request, 'experience');
    }

    // Common method to get items data
    private function getItemsData(Request $request, $type) {
        try {
            ResponseService::noPermissionThenSendJson('item-list');
            $offset = $request->input('offset', 0);
            $limit = $request->input('limit', 10);
            $sort = $request->input('sort', 'sequence');
            $order = $request->input('order', 'ASC');
            
            $sql = Item::with(['custom_fields', 'category:id,name', 'user:id,name', 'gallery_images', 'featured_items'])
                      ->where('provider_item_type', $type)
                      ->withTrashed();
                      
            if (!empty($request->search)) {
                $sql = $sql->search($request->search);
            }

            if (!empty($request->filter)) {
                $sql = $sql->filter(json_decode($request->filter, false, 512, JSON_THROW_ON_ERROR));
            }

            $total = $sql->count();
            $sql = $sql->sort($sort, $order)->skip($offset)->take($limit);
            $result = $sql->get();

            $bulkData = array();
            $bulkData['total'] = $total;
            $rows = array();

            $itemCustomFieldValues = ItemCustomFieldValue::whereIn('item_id', $result->pluck('id'))->get();
            foreach ($result as $row) {
                /* Merged ItemCustomFieldValue's data to main data */
                $itemCustomFieldValue = $itemCustomFieldValues->filter(function ($data) use ($row) {
                    return $data->item_id == $row->id;
                });

                $row->custom_fields = collect($row->custom_fields)->map(function ($customField) use ($itemCustomFieldValue) {
                    $customField['value'] = $itemCustomFieldValue->first(function ($data) use ($customField) {
                        return $data->custom_field_id == $customField->id;
                    });

                    if ($customField->type == "fileinput" && !empty($customField['value']->value)) {
                        if (!is_array($customField->value)) {
                            $customField['value'] = !empty($customField->value) ? [url(Storage::url($customField->value))] : [];
                        } else {
                            $customField['value'] = null;
                        }
                    }
                    return $customField;
                });
                $tempRow = $row->toArray();
                $operate = '';
                if (count($row->custom_fields) > 0 && Auth::user()->can('item-list')) {
                    // View Custom Field
                    $operate .= BootstrapTableService::button('fa fa-eye', '#', ['editdata', 'btn-light-danger  '], ['title' => __("View"), "data-bs-target" => "#editModal", "data-bs-toggle" => "modal",]);
                }

                if ($row->status !== 'sold out' && Auth::user()->can('item-update')) {
                    $operate .= BootstrapTableService::editButton(route('item.approval', $row->id), true, '#editStatusModal', 'edit-status', $row->id);
                }
                if (Auth::user()->can('item-delete')) {
                    $operate .= BootstrapTableService::deleteButton(route('item.destroy', $row->id));
                }
                $tempRow['active_status'] = empty($row->deleted_at);//IF deleted_at is empty then status is true else false
                $tempRow['operate'] = $operate;

                $rows[] = $tempRow;
            }
            $bulkData['rows'] = $rows;
            return response()->json($bulkData);

        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th, "ItemController --> getItemsData");
            ResponseService::errorResponse();
        }
    }

    // Original show method (can be kept for backward compatibility)
    public function show(Request $request) {
        try {
            ResponseService::noPermissionThenSendJson('item-list');
            $offset = $request->input('offset', 0);
            $limit = $request->input('limit', 10);
            $sort = $request->input('sort', 'sequence');
            $order = $request->input('order', 'ASC');
            $sql = Item::with(['custom_fields', 'category:id,name', 'user:id,name', 'gallery_images', 'featured_items'])->withTrashed();
            if (!empty($request->search)) {
                $sql = $sql->search($request->search);
            }

            if (!empty($request->filter)) {
                $sql = $sql->filter(json_decode($request->filter, false, 512, JSON_THROW_ON_ERROR));
            }

            $total = $sql->count();
            $sql = $sql->sort($sort, $order)->skip($offset)->take($limit);
            $result = $sql->get();

            $bulkData = array();
            $bulkData['total'] = $total;
            $rows = array();

            $itemCustomFieldValues = ItemCustomFieldValue::whereIn('item_id', $result->pluck('id'))->get();
            foreach ($result as $row) {
                /* Merged ItemCustomFieldValue's data to main data */
                $itemCustomFieldValue = $itemCustomFieldValues->filter(function ($data) use ($row) {
                    return $data->item_id == $row->id;
                });

                $row->custom_fields = collect($row->custom_fields)->map(function ($customField) use ($itemCustomFieldValue) {
                    $customField['value'] = $itemCustomFieldValue->first(function ($data) use ($customField) {
                        return $data->custom_field_id == $customField->id;
                    });

                    if ($customField->type == "fileinput" && !empty($customField['value']->value)) {
                        if (!is_array($customField->value)) {
                            $customField['value'] = !empty($customField->value) ? [url(Storage::url($customField->value))] : [];
                        } else {
                            $customField['value'] = null;
                        }
                    }
                    return $customField;
                });
                $tempRow = $row->toArray();
                $operate = '';
                if (count($row->custom_fields) > 0 && Auth::user()->can('item-list')) {
                    // View Custom Field
                    $operate .= BootstrapTableService::button('fa fa-eye', '#', ['editdata', 'btn-light-danger  '], ['title' => __("View"), "data-bs-target" => "#editModal", "data-bs-toggle" => "modal",]);
                }

                if ($row->status !== 'sold out' && Auth::user()->can('item-update')) {
                    $operate .= BootstrapTableService::editButton(route('item.approval', $row->id), true, '#editStatusModal', 'edit-status', $row->id);
                }
                if (Auth::user()->can('item-delete')) {
                    $operate .= BootstrapTableService::deleteButton(route('item.destroy', $row->id));
                }
                $tempRow['active_status'] = empty($row->deleted_at);//IF deleted_at is empty then status is true else false
                $tempRow['operate'] = $operate;

                $rows[] = $tempRow;
            }
            $bulkData['rows'] = $rows;
            return response()->json($bulkData);

        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th, "ItemController --> show");
            ResponseService::errorResponse();
        }
    }

    // Method to get a single item (if needed)
    public function showItem($id) {
        try {
            ResponseService::noPermissionThenSendJson('item-list');
            $item = Item::with(['custom_fields', 'category:id,name', 'user:id,name', 'gallery_images', 'featured_items'])->findOrFail($id);
            return response()->json($item);
        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th, "ItemController --> showItem");
            ResponseService::errorResponse();
        }
    }

    public function updateItemApproval(Request $request, $id) {
        try {
            ResponseService::noPermissionThenSendJson('item-update');
            $item = Item::with('user')->withTrashed()->findOrFail($id);
            
            // Log the incoming request data for debugging
            Log::info('Item approval update request:', ['id' => $id, 'data' => $request->all()]);
            
            // Update the item with all request data except is_featured (handle separately)
            $updateData = $request->except('is_featured');

            $old_item_status_is_same_as_new_one = ($item->status == $request->status);

            $updateData['rejected_reason'] = ($request->status == "rejected") ? $request->rejected_reason : '';
            
            $item->update($updateData);
            
            // Handle featured status
            if ($request->has('is_featured')) {
                Log::info('Featured status update:', ['is_featured' => $request->is_featured, 'item_id' => $id]);
                
                // Convert to boolean/integer for consistency
                $is_featured = filter_var($request->is_featured, FILTER_VALIDATE_BOOLEAN) || $request->is_featured === '1' || $request->is_featured === 1;
                
                if ($is_featured) {
                    // Check if an entry already exists in featured_items
                    $featuredItem = FeaturedItems::where('item_id', $id)->first();
                    
                    if (!$featuredItem) {
                        // First try to get an advertisement package for the admin
                        $userPackage = UserPurchasedPackage::where('user_id', Auth::id())
                            ->whereHas('package', function ($q) {
                                $q->where('type', 'advertisement');
                            })
                            ->first();
                        
                        // If no package found for admin, get first advertisement package in the system
                        if (!$userPackage) {
                            Log::warning('No valid user purchased package found for featuring item:', ['item_id' => $id]);
                            
                            // Find any user with an advertisement package to use
                            $anyUserPackage = UserPurchasedPackage::whereHas('package', function ($q) {
                                $q->where('type', 'advertisement');
                            })->first();
                            
                            if ($anyUserPackage) {
                                Log::info('Using existing user package for featuring:', ['package_id' => $anyUserPackage->package_id]);
                                
                                // Use an existing user's package
                                FeaturedItems::create([
                                    'item_id' => $id,
                                    'package_id' => $anyUserPackage->package_id,
                                    'user_purchased_package_id' => $anyUserPackage->id,
                                    'start_date' => date('Y-m-d'),
                                    'end_date' => $anyUserPackage->end_date
                                ]);
                            } else {
                                // No packages found in the system, let's create one for the admin
                                Log::info('No packages found, creating default package for admin');
                                
                                // Find or create a default advertisement package
                                $package = \App\Models\Package::firstOrCreate(
                                    ['type' => 'advertisement'],
                                    [
                                        'name' => 'Default Admin Ad Package',
                                        'price' => 0,
                                        'duration' => 30, // 30 days by default
                                        'status' => 1,
                                    ]
                                );
                                
                                // Create a user purchased package for the admin
                                $adminPackage = UserPurchasedPackage::create([
                                    'user_id' => Auth::id(),
                                    'package_id' => $package->id,
                                    'price' => 0,
                                    'start_date' => date('Y-m-d'),
                                    'end_date' => date('Y-m-d', strtotime('+30 days')),
                                    'payment_method' => 'system',
                                    'status' => 'completed'
                                ]);
                                
                                Log::info('Created default admin package:', ['package_id' => $package->id, 'user_package_id' => $adminPackage->id]);
                                
                                // Now use this package to feature the item
                                FeaturedItems::create([
                                    'item_id' => $id,
                                    'package_id' => $package->id,
                                    'user_purchased_package_id' => $adminPackage->id,
                                    'start_date' => date('Y-m-d'),
                                    'end_date' => $adminPackage->end_date
                                ]);
                            }
                        } else {
                            Log::info('Using admin package for featuring item:', ['package_id' => $userPackage->package_id]);
                            
                            // Create new featured item entry with admin's package
                            FeaturedItems::create([
                                'item_id' => $id,
                                'package_id' => $userPackage->package_id,
                                'user_purchased_package_id' => $userPackage->id,
                                'start_date' => date('Y-m-d'),
                                'end_date' => $userPackage->end_date
                            ]);
                        }
                    } else {
                        Log::info('Item already featured:', ['featured_item_id' => $featuredItem->id]);
                    }
                } else {
                    // Remove featured item entries for this item
                    $deleted = FeaturedItems::where('item_id', $id)->delete();
                    Log::info('Removed featured status:', ['item_id' => $id, 'deleted_count' => $deleted]);
                }
            }

            if(!$old_item_status_is_same_as_new_one) {

                // Send notifications
                $user_token = UserFcmToken::where('user_id', $item->user->id)->pluck('fcm_token')->toArray();

                // Create notification in the database
                $notificationTitle = 'About ' . $item->name;
                $notificationMessage = "Your Item is " . ucfirst($request->status);

                // Create notification record
                Notifications::create([
                    'title' => $notificationTitle,
                    'message' => $notificationMessage,
                    'item_id' => $item->id,
                    'user_id' => $item->user->id,
                    'send_to' => 'selected',
                    'image' => ''
                ]);
                // Send FCM notification
                if (!empty($user_token)) {
                    NotificationService::sendFcmNotification($user_token, $notificationTitle, $notificationMessage, "item-update", ['id' => $item->id]);
                }

            }
            return ResponseService::successResponse('Item Updated Successfully');
        } catch (Throwable $th) {
            Log::error('Error in updateItemApproval:', ['exception' => $th->getMessage(), 'trace' => $th->getTraceAsString()]);
            ResponseService::logErrorResponse($th, 'ItemController ->updateItemApproval');
            return ResponseService::errorResponse('Something Went Wrong: ' . $th->getMessage());
        }
    }

    public function destroy($id) {
        ResponseService::noPermissionThenSendJson('item-delete');

        try {
            $item = Item::with('gallery_images')->withTrashed()->findOrFail($id);
            foreach ($item->gallery_images as $gallery_image) {
                FileService::delete($gallery_image->getRawOriginal('image'));
            }
            FileService::delete($item->getRawOriginal('image'));

            $item->forceDelete();

            ResponseService::successResponse('Item deleted successfully');
        } catch (Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse('Something went wrong');
        }
    }
}
