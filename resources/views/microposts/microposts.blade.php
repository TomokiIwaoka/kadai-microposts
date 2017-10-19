<ul class="media-list">
@foreach ($microposts as $micropost)
    <?php $user = $micropost->user; ?>
    <li class="media">
        <div class="media-left">
            <img class="media-object img-rounded" src="{{ Gravatar::src($user->email, 50) }}" alt="">
        </div>
        <div class="media-body">
            <div>
                {!! link_to_route('users.show', $user->name, ['id' => $user->id]) !!} <span class="text-muted">posted at {{ $micropost->created_at }}</span>
            </div>
            <div>
                <p>{!! nl2br(e($micropost->content)) !!}</p>
            </div>
            <div>
                <!--以下理由により、いったんコメントアウト-->
                <!--@if (Auth::user()->id == $micropost->user_id)-->
                <!--    {!! Form::open(['route' => ['microposts.destroy', $micropost->id], 'method' => 'delete']) !!}-->
                <!--        {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-xs']) !!}-->
                <!--    {!! Form::close() !!}-->
                <!--@endif-->
            </div>
            <ul class="list-inline">
                <!--ログインユーザーが特定のMicropostにlikeしてなければlikeボタン、Likeしていればunlikeを表示-->
                
                <!--親レコードを削除しようとすると外部キーの制約からエラーになるので、Likeしてたら削除できなくする-->
                @if (Auth::user()->already_liked($micropost->id))
                    <li>
                        {!! Form::open(['route' => ['micropost.unlike', $micropost->id], 'method' => 'delete']) !!}
                             {!! Form::submit('Unlike', ['class' => "btn btn-danger btn-xs"]) !!}
                        {!! Form::close() !!}
                    </li>
                @else
                    <!--Likeボタンを表示-->
                    <li>
                        {!! Form::open(['route' => ['micropost.like', $micropost->id]]) !!}
                            {!! Form::submit('Like', ['class' => "btn btn-primary btn-xs"]) !!}
                        {!! Form::close() !!}
                    </li>
                    <!--ログインユーザーが作成したPostであれば、削除ボタンを表示-->
                    @if (Auth::user()->id == $micropost->user_id)
                        <li>
                            {!! Form::open(['route' => ['microposts.destroy', $micropost->id], 'method' => 'delete']) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-xs']) !!}
                            {!! Form::close() !!}
                        </li>
                    @endif
            　　@endif
            </ul>
        </div>
    </li>
@endforeach
</ul>
{!! $microposts->render() !!}・