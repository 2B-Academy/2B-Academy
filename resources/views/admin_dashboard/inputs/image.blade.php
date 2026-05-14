<label>
    <label class="form-label">  {{$label_name}} @if($required && !is_object($content)) <span class="text-danger">*</span>@endif </label>
</label>
<div class="uploadAndPreviewImage align-items-center row m-0">
    <div class="col-md-9 text-center">
        <label class="form-label cursor-pointer d-flex align-items-center justify-content-center gap-3" for="{{$input_name}}">
            <label class="upload" for="{{$input_name}}">
                <i class="bx bx-upload fs-1"></i>
            </label>
            <small class="text-danger">
                @if($accept == 'images_only')
                    (PNG - JPG - JPEG - WEBP - SVG - GIF) - (2MB max)
                @elseif($accept == 'files_only')
                    (PDF - DOC - DOCX - XLSX) - (2MB max)
                @elseif($accept == 'both')
                   (PNG - JPG - JPEG - WEBP - SVG - GIF - PDF - DOC - DOCX - XLSX) - (2MB max)
                @elseif($accept == 'videos')
                    (MP4) - (1GB max)
                @endif
            </small>
        </label>
        <input class="form-control p-0 fileInput" style="opacity: 0;" id="{{$input_name}}" type="file" name="{{$input_name}}" @if($required && !is_object($content)) required @endif >
    </div>
    <div class="col-md-3">
        <div class="preview_{{$input_name}} text-center">
            @if($content->id > 0 && $content->$input_name)
                <img src="{{$content->getFileUrl($content->$input_name)}}" width="100%">
            @else
                <img src="{{asset('admin_dashboard/assets/images/no_image.png')}}" width="100%">
            @endif
        </div>
    </div>
</div>
