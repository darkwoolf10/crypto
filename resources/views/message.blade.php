<div class="alert alert-primary">
    <input type="hidden" value="{{$message->id}}" id="messageId">
    <p style="word-wrap: break-word;">
        message: @if(empty($message->message))
                     Сообщение слишком большое для вывода.
                 @else
                    {{$message->message}}
                 @endif
    </p>
    <hr>
    <p style="word-wrap: break-word;">
        encrypt message: @if(empty($message->encode))
                             Сообщение слишком большое для вывода.
                         @else
                             {{$message->encode}}
                         @endif
    </p>
    <hr>
    <p>crypt type: {{$message->type}}</p>
    <hr>
    <p>crypt time: {{$message->time}}</p>
    <hr>
    <div class="float-left">Created in {{$message->created_at}}</div>
    <div class="float-right"><button class="btn btn-success" id="decrypt">Расшифровать</button></div>
    <br>
    <br>
</div>