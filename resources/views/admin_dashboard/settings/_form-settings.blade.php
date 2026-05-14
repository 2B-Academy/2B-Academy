@foreach($content as $con)
    @switch($con->type)
        @case('file')
            <div class="col-md-6 mb-3">
                <label class="form-label"> {{$con->label}}  </label>
                <input class="form-control" type="{{$con->type}}" name="settings[{{$con->key}}]">
            </div>
            @if($con->value)
                <div class="col-md-6 mb-3">
                    <div class="preview">
                        <img src="{{$con->getFileUrl($con->value)}}" width="100">
                    </div>
                </div>
            @endif
            @break
        @case('textarea')
            <div class="col-md-12 mb-3">
                <label class="form-label"> {{$con->label}} </label>
                <textarea cols="2" rows="2" class="tiny" name="settings[{{$con->key}}]">{!! $con->value !!}</textarea>
            </div>
            @break
        @case(in_array($con->type , ['text', 'number', 'url']))
            <div class="col-md-6 mb-3">
                <label class="form-label"> {{$con->label}}  </label>
                <input type="{{$con->type}}" name="settings[{{$con->key}}]" class="form-control"  value="{{$con->value}}">
            </div>
            @break
    @endswitch
@endforeach
