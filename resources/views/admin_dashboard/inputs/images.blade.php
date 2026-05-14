<div class="uploadAndPreviewImage align-items-center row">
    <div class="col-md-12 text-center">
        <label class="upload" for="upload_imgs">
            <i class="bx bx-upload"></i>
        </label>
        <label class="form-label cursor-pointer" for="upload_imgs">
            <h6 class="my-2"><strong>{{$label_name}}</strong></h6>
            <small class="text-danger">
                @if($accept == 'images_only')
                    (PNG - JPG - JPEG - WEBP - SVG - GIF) - (2MB max)
                @elseif($accept == 'files_only')
                    (PDF - DOC - DOCX - XLSX) - (2MB max)
                @elseif($accept == 'both')
                    (PNG - JPG - JPEG - WEBP - SVG - GIF - PDF - DOC - DOCX - XLSX) - (2MB max)
                @endif
            </small>
        </label>
        <input class="form-control fileInput" style="opacity: 0;" id="upload_imgs" type="file" name="{{$input_name}}" @if($required && !is_object($content)) required @endif  @if($multiple) multiple @endif>
    </div>
    <div class="col-md-12">
        <div class="quote-imgs-thumbs quote-imgs-thumbs--hidden" id="img_preview" aria-live="polite"></div>
    </div>
</div>
