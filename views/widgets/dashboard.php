@hasaccess?('comments', 'view')
<style>
    .comments-list {
        border: none;
        resize: none;
    }
    .comment-post {
        line-height: 13px;
        font-size: 13px;
    }
    .comment-author {
        min-width: 100px;
        font-size: 10px;
    }
    .comment-author .uk-margin-small-left {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
</style>
<div>

    <div class="uk-panel-box uk-panel-card">

        <div class="uk-panel-box-header uk-flex">
            <strong class="uk-panel-box-header-title uk-flex-item-1">
                {{ count($comments) }} @lang('Comment(s) in the last 7 days')
            </strong>
        </div>

        @if(count($comments))
            <div class="uk-margin comments-container">
                <ul class="uk-list uk-list-space uk-margin-top uk-scrollable-box comments-list">
                    @foreach($comments as $comment)
                    <li class="uk-flex">
                        <div riot-mount class="uk-flex-item comment-author uk-margin-small-right">
                            <cp-account size="24" account="{{ $comment['_creator'] }}"></cp-account>
                        </div>
                         <div class="uk-flex-item-1">
                            <div class="uk-text-small comment-post">
                                <a href="@route('/collections/entry/'.$comment['_collection'].'/'.$comment['_oid'].'/#comments')" class="uk-text-muted">{{ mb_strimwidth($comment['post'], 0, 72, "…") }}</a>
                            </div>
                            <span class="uk-badge-contrast uk-text-small uk-text-muted">{{ date('M dS Y H:i', $comment['_created']) }}</span>
                        </div>
                        <div class="uk-flex-item">
                            <a href="@route('/collections/entry/'.$comment['_collection'].'/'.$comment['_oid'].'/#comments')" class="uk-text-muted">
                                <i class="uk-icon-eye"></i>
                            </a>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="uk-margin uk-text-center uk-text-muted">
                <p><i class="uk-icon-justify uk-icon-comment" style="font-size: 25px"></i></p>
                @lang('No comments in collection entries you authored!').
            </div>
        @endif
    </div>

</div>
@end
