<div class="alert alert-primary">
    <p style="word-wrap: break-word;">message: {{$message->message}}</p>
    <hr>
    <p style="word-wrap: break-word;">encrypt message: {{$message->encode}}</p>
    <hr>
    <p>crypt type: {{$message->type}}</p>
    <hr>
    <p>crypt time: {{$message->time}}</p>
    <hr>
    <div class="float-right">Created in {{$message->created_at}}</div>
    <br>
</div>