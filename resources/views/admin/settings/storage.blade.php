<div class="card-style settings-card-2 mb-30">
    <form action="{{ route('admin.settings.storage') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-12">
                <h4 class="mb-3">{{ trans('storage_settings') }}</h4>
                <hr>
            </div>
            
            <!-- Storage Driver Selection -->
            <div class="col-12">
                <div class="select-style-1">
                    <label>{{ trans('storage_driver') }}</label>
                    <div class="select-position">
                        <select name="storage_driver" class="light-bg" id="storage_driver">
                            <option value="local" {{ get_setting('storage_driver') == "local" ? ' selected' : '' }}>{{ trans('local_storage') }}</option>
                            <option value="s3" {{ get_setting('storage_driver') == "s3" ? ' selected' : '' }}>Amazon S3</option>
                            <option value="wasabi" {{ get_setting('storage_driver') == "wasabi" ? ' selected' : '' }}>Wasabi</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- S3 Configuration -->
            <div class="col-12 s3-config" style="{{ get_setting('storage_driver') != 's3' ? 'display:none;' : '' }}">
                <hr>
                <h5 class="mb-3">Amazon S3 Configuration</h5>
                
                <div class="select-style-1 mb-3">
                    <label>{{ trans('enable_s3') }}</label>
                    <div class="select-position">
                        <select name="enable_s3" class="light-bg">
                            <option value="1" {{ get_setting('enable_s3') == "1" ? ' selected' : '' }}>{{ trans('yes') }}</option>
                            <option value="0" {{ get_setting('enable_s3') == "0" ? ' selected' : '' }}>{{ trans('no') }}</option>
                        </select>
                    </div>
                </div>
                
                <div class="input-style-1">
                    <label>AWS Access Key ID</label>
                    <input type="text" name="aws_access_key_id" value="{{ get_setting('aws_access_key_id') }}" placeholder="AWS Access Key ID" />
                </div>
                
                <div class="input-style-1">
                    <label>AWS Secret Access Key</label>
                    <input type="password" name="aws_secret_access_key" value="{{ get_setting('aws_secret_access_key') }}" placeholder="AWS Secret Access Key" />
                </div>
                
                <div class="input-style-1">
                    <label>AWS Region</label>
                    <input type="text" name="aws_default_region" value="{{ get_setting('aws_default_region', 'us-east-1') }}" placeholder="us-east-1" />
                </div>
                
                <div class="input-style-1">
                    <label>AWS Bucket</label>
                    <input type="text" name="aws_bucket" value="{{ get_setting('aws_bucket') }}" placeholder="Bucket Name" />
                </div>
                
                <div class="input-style-1">
                    <label>AWS Endpoint (Optional)</label>
                    <input type="text" name="aws_endpoint" value="{{ get_setting('aws_endpoint') }}" placeholder="Leave empty for default" />
                </div>
            </div>
            
            <!-- Wasabi Configuration -->
            <div class="col-12 wasabi-config" style="{{ get_setting('storage_driver') != 'wasabi' ? 'display:none;' : '' }}">
                <hr>
                <h5 class="mb-3">Wasabi Configuration</h5>
                
                <div class="select-style-1 mb-3">
                    <label>{{ trans('enable_wasabi') }}</label>
                    <div class="select-position">
                        <select name="enable_wasabi" class="light-bg">
                            <option value="1" {{ get_setting('enable_wasabi') == "1" ? ' selected' : '' }}>{{ trans('yes') }}</option>
                            <option value="0" {{ get_setting('enable_wasabi') == "0" ? ' selected' : '' }}>{{ trans('no') }}</option>
                        </select>
                    </div>
                </div>
                
                <div class="input-style-1">
                    <label>Wasabi Access Key</label>
                    <input type="text" name="wasabi_access_key_id" value="{{ get_setting('wasabi_access_key_id') }}" placeholder="Wasabi Access Key" />
                </div>
                
                <div class="input-style-1">
                    <label>Wasabi Secret Key</label>
                    <input type="password" name="wasabi_secret_access_key" value="{{ get_setting('wasabi_secret_access_key') }}" placeholder="Wasabi Secret Key" />
                </div>
                
                <div class="input-style-1">
                    <label>Wasabi Region</label>
                    <input type="text" name="wasabi_default_region" value="{{ get_setting('wasabi_default_region', 'us-east-1') }}" placeholder="us-east-1" />
                </div>
                
                <div class="input-style-1">
                    <label>Wasabi Bucket</label>
                    <input type="text" name="wasabi_bucket" value="{{ get_setting('wasabi_bucket') }}" placeholder="Bucket Name" />
                </div>
                
                <div class="input-style-1">
                    <label>Wasabi Endpoint</label>
                    <input type="text" name="wasabi_endpoint" value="{{ get_setting('wasabi_endpoint', 'https://s3.wasabisys.com') }}" placeholder="https://s3.wasabisys.com" />
                </div>
            </div>
            
            <!-- CDN Configuration -->
            <div class="col-12">
                <hr>
                <h5 class="mb-3">CDN Configuration</h5>
                
                <div class="select-style-1 mb-3">
                    <label>{{ trans('enable_cdn') }}</label>
                    <div class="select-position">
                        <select name="enable_cdn" class="light-bg">
                            <option value="1" {{ get_setting('enable_cdn') == "1" ? ' selected' : '' }}>{{ trans('yes') }}</option>
                            <option value="0" {{ get_setting('enable_cdn') == "0" ? ' selected' : '' }}>{{ trans('no') }}</option>
                        </select>
                    </div>
                </div>
                
                <div class="input-style-1">
                    <label>CDN URL (for uploads)</label>
                    <input type="url" name="cdn_url" value="{{ get_setting('cdn_url') }}" placeholder="https://cdn.example.com" />
                </div>
                
                <div class="input-style-1">
                    <label>CDN Assets URL (for CSS/JS - optional)</label>
                    <input type="url" name="cdn_assets_url" value="{{ get_setting('cdn_assets_url') }}" placeholder="https://assets.example.com" />
                </div>
            </div>
            
            <!-- Upload Settings -->
            <div class="col-12">
                <hr>
                <h5 class="mb-3">Upload Settings</h5>
                
                <div class="input-style-1">
                    <label>Max Upload Size (MB)</label>
                    <input type="number" name="max_upload_size_mb" value="{{ get_setting('max_upload_size_mb', '20') }}" placeholder="20" />
                </div>
                
                <div class="input-style-1">
                    <label>Allowed Image Extensions</label>
                    <input type="text" name="allowed_image_extensions" value="{{ get_setting('allowed_image_extensions', 'jpg,jpeg,png,gif,webp') }}" placeholder="jpg,jpeg,png,gif,webp" />
                </div>
                
                <div class="input-style-1">
                    <label>Allowed Video Extensions</label>
                    <input type="text" name="allowed_video_extensions" value="{{ get_setting('allowed_video_extensions', 'mp4,webm,ogg') }}" placeholder="mp4,webm,ogg" />
                </div>
                
                <div class="input-style-1">
                    <label>Allowed Document Extensions</label>
                    <input type="text" name="allowed_document_extensions" value="{{ get_setting('allowed_document_extensions', 'pdf,doc,docx,xls,xlsx') }}" placeholder="pdf,doc,docx,xls,xlsx" />
                </div>
            </div>
            
            <!-- Image Processing -->
            <div class="col-12">
                <hr>
                <h5 class="mb-3">Image Processing</h5>
                
                <div class="select-style-1 mb-3">
                    <label>{{ trans('optimize_images') }}</label>
                    <div class="select-position">
                        <select name="optimize_images" class="light-bg">
                            <option value="1" {{ get_setting('optimize_images') == "1" ? ' selected' : '' }}>{{ trans('yes') }}</option>
                            <option value="0" {{ get_setting('optimize_images') == "0" ? ' selected' : '' }}>{{ trans('no') }}</option>
                        </select>
                    </div>
                </div>
                
                <div class="input-style-1">
                    <label>Max Image Width (px)</label>
                    <input type="number" name="image_max_width" value="{{ get_setting('image_max_width', '1920') }}" placeholder="1920" />
                </div>
                
                <div class="input-style-1">
                    <label>Image Quality (%)</label>
                    <input type="number" name="image_quality" value="{{ get_setting('image_quality', '85') }}" min="1" max="100" placeholder="85" />
                </div>
            </div>
            
            <div class="col-12">
                <button type="submit" class="main-btn primary-btn btn-hover">
                    {{ trans('save_settings') }}
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const storageDriver = document.getElementById('storage_driver');
    const s3Config = document.querySelector('.s3-config');
    const wasabiConfig = document.querySelector('.wasabi-config');
    
    function toggleStorageConfig() {
        const value = storageDriver.value;
        s3Config.style.display = value === 's3' ? 'block' : 'none';
        wasabiConfig.style.display = value === 'wasabi' ? 'block' : 'none';
    }
    
    storageDriver.addEventListener('change', toggleStorageConfig);
});
</script>
