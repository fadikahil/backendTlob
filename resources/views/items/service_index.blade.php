@extends('layouts.main')

@section('title')
    {{ __('Service Item Management') }}
@endsection

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h4>@yield('title')</h4>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first"></div>
        </div>
    </div>
@endsection

@section('content')
    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div id="filters">
                            <label for="filter">{{__("Status")}}</label>
                            <select class="form-control bootstrap-table-filter-control-status" id="filter">
                                <option value="">{{__("All")}}</option>
                                <option value="review">{{__("Under Review")}}</option>
                                <option value="approved">{{__("Approved")}}</option>
                                <option value="rejected">{{__("Rejected")}}</option>
                                <option value="sold out">{{__("Sold Out")}}</option>
                                <option value="expired">{{__("Expired")}}</option>
                                <option value="inactive">{{__("Inactive")}}</option>
                            </select>
                        </div>
                        <table class="table-borderless table-striped" aria-describedby="mydesc" id="table_list"
                               data-toggle="table" data-url="{{ route('service.items.data') }}" data-click-to-select="true"
                               data-side-pagination="server" data-pagination="true"
                               data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                               data-show-columns="true" data-show-refresh="true" data-fixed-columns="true"
                               data-fixed-number="1" data-fixed-right-number="1" data-trim-on-search="false"
                               data-escape="true"
                               data-responsive="true" data-sort-name="id" data-sort-order="desc"
                               data-pagination-successively-size="3" data-table="items" data-status-column="deleted_at"
                               data-show-export="true"
                               data-export-options='{"fileName": "service-item-list","ignoreColumn": ["operate"]}'
                               data-export-types="['pdf','json', 'xml', 'csv', 'txt', 'sql', 'doc', 'excel']"
                               data-mobile-responsive="true" data-filter-control="true" data-filter-control-container="#filters" data-toolbar="#filters">
                            <thead class="thead-dark">
                            <tr>
                                <th scope="col" data-field="id" data-align="center" data-sortable="true">{{ __('ID') }}</th>
                                <th scope="col" data-field="name" data-align="center" data-sortable="true">{{ __('Name') }}</th>
                                <th scope="col" data-field="description" data-align="center" data-sortable="true" data-formatter="descriptionFormatter">{{ __('Description') }}</th>
                                <th scope="col" data-field="user.name" data-align="center" data-sort-name="user_name" data-sortable="true">{{ __('User') }}</th>
                                <th scope="col" data-field="category.name" data-align="center" data-sort-name="category_name" data-sortable="true">{{ __('Category') }}</th>
                                <th scope="col" data-field="price" data-align="center" data-sortable="true">{{ __('Price') }}</th>
                                <th scope="col" data-field="image" data-align="center" data-sortable="false" data-escape="false" data-formatter="imageFormatter">{{ __('Image') }}</th>
                                <th scope="col" data-field="gallery_images" data-align="center" data-sortable="false" data-formatter="galleryImageFormatter" data-escape="false">{{ __('Other Images') }}</th>
                                <th scope="col" data-field="latitude" data-sortable="true" data-visible="true">{{ __('Latitude') }}</th>
                                <th scope="col" data-field="longitude" data-sortable="true" data-visible="true">{{ __('Longitude') }}</th>
                                <th scope="col" data-field="address" data-sortable="true" data-visible="false">{{ __('Address') }}</th>
                                <th scope="col" data-field="contact" data-sortable="true" data-visible="false">{{ __('Contact') }}</th>
                                <th scope="col" data-field="country" data-align="center" data-sortable="true" data-visible="true">{{ __('Country') }}</th>
                                <th scope="col" data-field="state" data-align="center" data-sortable="true" data-visible="true">{{ __('State') }}</th>
                                <th scope="col" data-field="city" data-align="center" data-sortable="true" data-visible="true">{{ __('City') }}</th>
                                <th scope="col" data-field="featured_items" data-formatter="featuredFormatter" data-align="center">{{ __('Featured/Premium') }}</th>
                                
                                <!-- SPECIAL TAGS COLUMN -->
                                <th scope="col"
                                    data-field="special_tags"
                                    data-align="center"
                                    data-sortable="true"
                                    data-formatter="specialTagsFormatter">
                                    {{ __('Special Tags') }}
                                </th>
                                
                                <th scope="col" data-field="status" data-align="center" data-sortable="true" data-filter-control="select" data-filter-data="" data-escape="false" data-formatter="itemStatusFormatter">{{ __('Status') }}</th>
                                <th scope="col" data-field="rejected_reason" data-sortable="true" data-visible="true">{{ __('Rejected Reason') }}</th>
                                <th scope="col" data-field="expiry_date" data-align="center" data-sortable="true">{{ __('Expiry Date') }}</th>
                                <th scope="col" data-field="created_at" data-sortable="true" data-visible="false">{{ __('Created At') }}</th>
                                <th scope="col" data-field="updated_at" data-sortable="true" data-visible="false">{{ __('Updated At') }}</th>
                                <th scope="col" data-field="user_id" data-sortable="true" data-visible="false">{{ __('User ID') }}</th>
                                <th scope="col" data-field="category_id" data-sortable="true" data-visible="false">{{ __('Category ID') }}</th>
                                <th scope="col" data-field="likes" data-sortable="true" data-visible="false">{{ __('Likes') }}</th>
                                <th scope="col" data-field="clicks" data-sortable="true" data-visible="false">{{ __('Clicks') }}</th>
                                @canany(['item-update','item-delete'])
                                    <th scope="col" data-field="operate" data-align="center" data-sortable="false" data-events="itemEvents" data-escape="false">{{ __('Action') }}</th>
                                @endcanany
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Item details modal -->
        <div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myModalLabel1">{{ __('Item Details') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="center" id="custom_fields"></div>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        
        <!-- Status change modal -->
        <div id="editStatusModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myModalLabel1">{{ __('Status') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="edit-status-form" class="edit-form" action="" method="POST">
                            @csrf
                            <!-- Featured option -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="is_featured" class="form-label">{{ __('Featured Item') }}</label>
                                    <select class="form-select" id="is_featured" name="is_featured">
                                        <option value="0">{{ __('No') }}</option>
                                        <option value="1">{{ __('Yes') }}</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <select name="status" class="form-select" id="status" aria-label="status">
                                        <option value="review">{{__("Under Review")}}</option>
                                        <option value="approved">{{__("Approve")}}</option>
                                        <option value="rejected">{{__("Reject")}}</option>
                                    </select>
                                </div>
                            </div>
                            <div id="rejected_reason_container" class="col-md-12" style="display: none;">
                                <label for="rejected_reason" class="mandatory form-label">{{ __('Reason') }}</label>
                                <textarea name="rejected_reason" id="rejected_reason" class="form-control" placeholder={{ __('Reason') }}></textarea>
                            </div>
                            <div id="status-error-message" class="text-danger mt-2" style="display: none;"></div>
                            <input type="submit" value="{{__("Save")}}" class="btn btn-primary mt-3">
                        </form>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </section>
@endsection

@section('js')
<script>
    function imageFormatter(value, row) {
        if (value) {
            return '<img src="' + value + '" alt="Item Image" class="img-thumbnail" style="max-width: 80px;">';
        }
        return '-';
    }
    
    // Simpler special tags formatter that handles HTML entities properly
    function specialTagsFormatter(value, row) {
        if (!value) return '-';
        
        try {
            // Decode HTML entities first, regardless of the format
            let decodedValue = decodeHtmlEntities(value);
            
            // Try to parse as JSON if it looks like a JSON string
            let tags = {};
            if (typeof decodedValue === 'string') {
                if (decodedValue.trim().startsWith('{') && decodedValue.includes(':')) {
                    try {
                        // Fix JSON format issues
                        let fixedJson = decodedValue
                            .replace(/'/g, '"')  // Replace single quotes with double quotes
                            .replace(/([{,])\s*([a-zA-Z0-9_]+)\s*:/g, '$1"$2":');  // Quote unquoted keys
                            
                        tags = JSON.parse(fixedJson);
                    } catch (e) {
                        console.log("JSON parse error:", e);
                        // Fall back to direct string formatting if parsing fails
                        return formatTagString(decodedValue);
                    }
                } else {
                    // Not a JSON object, format directly
                    return formatTagString(decodedValue);
                }
            } else if (typeof decodedValue === 'object') {
                tags = decodedValue;
            }
            
            // Format object tags
            if (typeof tags === 'object' && !Array.isArray(tags) && tags !== null) {
                let html = '<div style="text-align: left;">';
                
                for (let key in tags) {
                    // Format the key (convert snake_case to Title Case)
                    let formattedKey = key
                        .replace(/_/g, ' ')
                        .replace(/\b\w/g, c => c.toUpperCase());
                    
                    // Format the value
                    let formattedValue = '';
                    if (tags[key] === "true" || tags[key] === true) {
                        formattedValue = 'True';
                    } else if (tags[key] === "false" || tags[key] === false) {
                        formattedValue = 'False';
                    } else {
                        formattedValue = tags[key];
                    }
                    
                    // Add this tag to the output with clear labeling
                    html += '<div class="mb-1">' + formattedKey + ': ' + formattedValue + '</div>';
                }
                
                html += '</div>';
                return html || '-';
            }
            
            // Fallback
            return formatTagString(decodedValue);
            
        } catch (e) {
            console.error("Error formatting special tags:", e);
            return typeof value === 'string' ? formatTagString(value) : String(value);
        }
    }
    
    // Helper to decode HTML entities
    function decodeHtmlEntities(text) {
        if (typeof text !== 'string') return text;
        
        let tempDiv = document.createElement('div');
        tempDiv.innerHTML = text;
        return tempDiv.textContent || tempDiv.innerText || text;
    }
    
    // Format a tag string directly (not as JSON)
    function formatTagString(str) {
        if (typeof str !== 'string') return String(str);
        
        let formatted = str
            .replace(/[{}"]/g, '')  // Remove JSON syntax
            .replace(/&quot;/g, '')  // Remove HTML quotes
            .replace(/,/g, '<br>')   // Replace commas with line breaks
            .replace(/:/g, ': ')     // Add space after colons
            .replace(/_/g, ' ')      // Replace underscores with spaces
            .replace(/\b\w/g, c => c.toUpperCase());  // Title case words
            
        // Wrap in a div with left alignment
        return '<div style="text-align: left;">' + formatted + '</div>';
    }

    function galleryImageFormatter(value, row) {
        if (value && value.length > 0) {
            let html = '';
            for (let i = 0; i < Math.min(value.length, 2); i++) {
                html += '<img src="' + value[i].image + '" alt="Gallery Image" class="img-thumbnail" style="max-width: 50px; margin: 2px;">';
            }
            return html;
        }
        return '-';
    }
    
    function featuredFormatter(value, row) {
        if (value && value.length > 0) {
            return '<span class="badge bg-success">Featured</span>';
        }
        return '-';
    }
    
    function descriptionFormatter(value, row) {
        if (value) {
            return value.length > 50 ? value.substring(0, 50) + '...' : value;
        }
        return '-';
    }
    
    function itemStatusFormatter(value, row) {
        if (value === 'review') {
            return '<span class="badge bg-warning">{{ __("Under Review") }}</span>';
        } else if (value === 'approved') {
            return '<span class="badge bg-success">{{ __("Approved") }}</span>';
        } else if (value === 'rejected') {
            return '<span class="badge bg-danger">{{ __("Rejected") }}</span>';
        } else if (value === 'sold out') {
            return '<span class="badge bg-info">{{ __("Sold Out") }}</span>';
        } else if (value === 'expired') {
            return '<span class="badge bg-secondary">{{ __("Expired") }}</span>';
        } else if (value === 'inactive') {
            return '<span class="badge bg-dark">{{ __("Inactive") }}</span>';
        }
        return value;
    }
    
    function queryParams(params) {
        params.status = $('#filter').val();
        return params;
    }
    
    $(document).ready(function() {
        $('#filter').change(function() {
            $('#table_list').bootstrapTable('refresh');
        });
        
        $('#status').change(function() {
            if ($(this).val() === 'rejected') {
                $('#rejected_reason_container').show();
            } else {
                $('#rejected_reason_container').hide();
            }
        });
        
        // Handle the form submission with AJAX
        $('#edit-status-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            
            // Get the featured value
            var isFeatured = $('#is_featured').val();
            
            // Build the form data
            var formData = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                status: $('#status').val(),
                is_featured: isFeatured,
            };
            
            // Add rejected reason if present
            if ($('#status').val() === 'rejected') {
                formData.rejected_reason = $('#rejected_reason').val();
            }
            
            console.log('Form action:', url);
            console.log('Form data:', formData);
            
            $('#status-error-message').hide();
            
            $.ajax({
                url: url,
                type: 'PUT',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Check if toastr is defined
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        } else {
                            alert('Success: ' + response.message);
                        }
                        $('#editStatusModal').modal('hide');
                        $('#table_list').bootstrapTable('refresh');
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.message);
                        } else {
                            alert(response.message);
                        }
                        $('#status-error-message').text(response.message).show();
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    var errorMessage = '{{ __("An error occurred") }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                    $('#status-error-message').text(errorMessage).show();
                }
            });
        });
    });
    
    window.itemEvents = {
        'click .edit-item': function (e, value, row, index) {
            window.location.href = '{{ url("service-items") }}/' + row.id + '/edit';
        },
        'click .view-item': function (e, value, row, index) {
            $('#custom_fields').html('Loading...');
            $('#editModal').modal('show');
            
            $.ajax({
                url: '{{ route("item.show", ":id") }}'.replace(':id', row.id),
                type: 'GET',
                success: function(response) {
                    $('#custom_fields').html(response);
                },
                error: function() {
                    $('#custom_fields').html('{{ __("Error loading data") }}');
                }
            });
        },
        'click .change-status': function (e, value, row, index) {
            // Set the form action URL using Laravel's route helper
            $('#edit-status-form').attr('action', '{{ route("item.approval", ":id") }}'.replace(':id', row.id));
            $('#status-error-message').hide();
            
            // Set the current status value
            $('#status').val(row.status);
            
            // Check if item has featured_items entries
            let isFeatured = row.featured_items && row.featured_items.length > 0 ? '1' : '0';
            
            // Set the featured dropdown
            $('#is_featured').val(isFeatured);
            
            // Handle rejected reason container visibility
            if (row.status === 'rejected') {
                $('#rejected_reason_container').show();
                $('#rejected_reason').val(row.rejected_reason || '');
            } else {
                $('#rejected_reason_container').hide();
                $('#rejected_reason').val('');
            }
            
            // Show the modal
            $('#editStatusModal').modal('show');
        },
        'click .delete-item': function (e, value, row, index) {
            if (confirm('{{ __("Are you sure you want to delete this item?") }}')) {
                $.ajax({
                    url: '{{ url("item") }}/' + row.id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE',
                        type: 'service'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#table_list').bootstrapTable('refresh');
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        toastr.error('{{ __("An error occurred") }}');
                    }
                });
            }
        }
    };
</script>
@endsection
