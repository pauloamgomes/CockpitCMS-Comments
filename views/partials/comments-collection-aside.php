<style>
    .hide-items > .uk-margin:not(.comments) { display: none }
    .header { padding: 20px }
    .comments-list { background: #f5f5f5; border-radius: 0; max-width: 100%; margin: 0; overflow: auto; resize: none }
    .comment-post { font-size: 16px; line-height: 20px; white-space: pre-line }
    .comment-card:hover { cursor: pointer; border: 1px solid #000 }
    .comment-delete { position: absolute; top: 15px; right: 15px }
    .comments-form { border-radius: 0; padding: 15px; max-width: 100%; margin: 0 }
    .comments-form textarea { resize: none; padding: 5px }
</style>

<div if="{entry && entry._id}" class="uk-offcanvas" ref="comments">
    <div class="uk-offcanvas-bar uk-offcanvas-bar-flip uk-width-2-4 uk-flex uk-flex-column">
        <div class="uk-flex uk-flex-middle header">
            <span class="uk-text-bold">
                {entryComments.length > 0 ? entryComments.length + ' ' + App.i18n.get('Comments') : App.i18n.get('No Comments')}
            </span>
            <div class="uk-flex-item-1 uk-text-right">
                <a class="uk-offcanvas-close uk-link-muted uk-icon-close"></a>
            </div>
        </div>
        <div class="uk-text-small uk-flex-item-1 comments-list uk-scrollable-box" ref="commentsList">
            <div if="{ !entryComments.length }"  class="uk-text-muted uk-text-large">
                <i class="uk-icon-comment uk-icon-justify"></i> @lang('No one has commented yet.')
            </div>
            <div if="{ entryComments.length }" class="uk-text-muted">
                <div class="uk-margin">
                    <ul class="uk-nav">
                        <li each="{comment in entryComments}" class="uk-panel uk-panel-box uk-panel-card comment-card">
                            <div class="uk-flex uk-flex-wrap">
                                <div onclick="{expandComment}" class="uk-width-1-1 uk-margin-small-bottom">
                                    <cp-account size="32" account="{comment._creator}"></cp-account>
                                </div>
                                <div onclick="{expandComment}" class="uk-flex-item-1 uk-margin-small-right">
                                    <div class="uk-text-large comment-post" onclick="{comment.reply}">{ comment.post }</div>
                                    <div class="uk-margin-small-top">
                                        { App.Utils.dateformat(comment._created*1000, 'MMMM Do YYYY') }
                                        <span class="uk-text-small">
                                            { App.Utils.dateformat(comment._created*1000, 'HH:mm') }
                                        </span>
                                    </div>
                                </div>
                                @hasaccess?('comments', 'delete')
                                <div class="uk-flex-item comment-delete">
                                    <a onclick="{deleteComment}"><i class="uk-text-danger uk-icon-trash-o"></i></a>
                                </div>
                                @end
                                <div class="uk-width-4-4">
                                    <ul class="uk-nav">
                                        <li each="{reply in comment.replies}" class="uk-panel uk-panel-box uk-panel-card">
                                            <div class="uk-flex uk-flex-wrap">
                                                <div onclick="{expandReply}" class="uk-width-1-1 uk-margin-small-bottom">
                                                    <cp-account size="24" account="{reply._creator}"></cp-account>
                                                </div>
                                                <div onclick="{expandReply}" class="uk-flex-item-1 uk-margin-small-right">
                                                    <div class="uk-text-large comment-post">{ reply.post }</div>
                                                    <div class="uk-margin-small-top">
                                                        { App.Utils.dateformat(reply._created*1000, 'MMMM Do YYYY') }
                                                        <span class="uk-text-small">
                                                            { App.Utils.dateformat(reply._created*1000, 'HH:mm') }
                                                        </span>
                                                    </div>
                                                </div>
                                                @hasaccess?('comments', 'delete')
                                                <div class="uk-flex-item comment-delete">
                                                    <a onclick="{deleteReply}"><i class="uk-text-danger uk-icon-trash-o"></i></a>
                                                </div>
                                                @end
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            @hasaccess?('comments', 'add')
                            <div if="{expandedComment && expandedComment == comment._id}" class="comment-expanded">
                                <div class="uk-text-small uk-flex-item-2 comments-form">
                                    <textarea maxlength="300" ref="reply{comment._id}" class="uk-width-1-1 uk-margin-small-bottom" rows="4" placeholder="Reply to comment..."></textarea>
                                    <button onclick="{ createCommentReply }" class="uk-text-nowrap uk-button uk-button-small uk-button-primary">
                                        @lang('Reply')
                                    </button>
                                    <button onclick="{ cancelCommentReply }" class="uk-text-nowrap uk-button uk-button-small uk-button-secondary">
                                        @lang('Cancel')
                                    </button>
                                </div>
                            </div>
                            @end
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @hasaccess?('comments', 'add')
        <div class="uk-text-small uk-flex-item-2 comments-form">
            <textarea onclick="{ enableAddComment }" maxlength="300" ref="comment" class="uk-width-1-1 uk-margin-small-bottom" rows="{commenting ? 5 : 1}" placeholder="Create new comment..."></textarea>
            <button show="{ commenting }" onclick="{ createEntryComment }" class="uk-text-nowrap uk-button uk-button-small uk-button-primary">
                @lang('Create')
            </button>
            <button show="{ commenting }" onclick="{ cancelEntryComment }" class="uk-text-nowrap uk-button uk-button-small uk-button-secondary">
                @lang('Cancel')
            </button>
        </div>
        @end
    </div>

</div>

<script>
    var $this = this;
    this.expandedComment = null;
    this.entryComments = [];

    this.on('mount', function() {
        window.setTimeout(function() {
            if ($this.entry._id) {
                var headerElement = document.querySelector('.header-sub-panel .uk-flex');
                var link = document.createElement('a');
                link.classList = "uk-button uk-button-outline uk-text-warning uk-margin-small-left";
                link.href = "#";
                link.id = "comments-link";
                link.append('Loading comments...');
                link.onclick = function() {
                    $this.showEntryComments();
                };
                if (!headerElement.querySelector('.uk-flex-item-1')) {
                    var divider = document.createElement('div');
                    divider.classList = "uk-flex-item-1";
                    headerElement.append(divider);
                }
                headerElement.append(link);
                var hash = location.hash.replace(/^#/, '');
                if (hash === 'comments') {
                    window.setTimeout(function() {
                        location.hash = "";
                        $this.showEntryComments();
                    }, 200);
                }

                $this.getComments();
            }
        }, 50);
    });

    this.getComments = function() {
        App.callmodule('comments:get', this.entry._id, 'view').then(function(data) {
            $this.entryComments = data.result || [];
            $this.updateCommentsCount();
            $this.update();
        }).catch(function(e){
            App.ui.notify('Error retrieving comments for this entry', 'danger');
        });
    }

    this.updateCommentsCount = function() {
        var link = document.querySelector('#comments-link');
        link.text = $this.entryComments.length > 0 ? $this.entryComments.length + " Comment(s)" : "Comments";
    }

    this.showEntryComments = function() {
        UIkit.offcanvas.show($this.refs.comments);
        window.setTimeout(function() {
            $this.refs.commentsList.scrollTop = $this.refs.commentsList.scrollHeight + 50;
        }, 50);
    }

    this.enableAddComment = function() {
        this.commenting = true;
        this.update();
    }

    this.createEntryComment = function(e) {
        var data = {
            post: $this.refs.comment.value,
            oid: $this.entry._id
        };
        $this.saveComment(data)
    }

    this.createCommentReply = function(e) {
        var data = {
            post: $this.refs["reply" + e.item.comment._id].value,
            oid: $this.entry._id,
            pid: e.item.comment._id
        }
        $this.saveComment(data)
    }

    this.saveComment = function(comment) {
        if (!comment.post || comment.post.length < 3) {
            App.ui.notify('Invalid comment', 'danger');
        } else {
            comment.collection = $this.collection.name;
            App.callmodule('comments:create', comment, 'add').then(function(data) {
                App.ui.notify('New comment added!', 'success');
                $this.entryComments = data.result || [];
                $this.refs.comment.value = '';
                if (comment.oid && $this.refs["reply" + comment.oid]) {
                    $this.refs["reply" + comment.oid].value = '';
                }
                $this.expandedComment = null;
                $this.updateCommentsCount();
                if (!comment.pid) {
                    $this.refs.commentsList.scrollTop = $this.refs.commentsList.scrollHeight + 50;
                }
                $this.update();
            }).catch(function(e){
                App.ui.notify('Error creating comment! Try again later.', 'danger');
            });
        }
    }

    this.deleteComment = function(e) {
        $this.deleteCommentPost([e.item.comment._id, $this.entry._id]);
    }

    this.deleteReply = function(e) {
        $this.deleteCommentPost([e.item.reply._id, $this.entry._id]);
    }

    this.deleteCommentPost = function(comment) {
        App.ui.confirm("Are you sure?", function() {
            App.callmodule('comments:delete', comment, 'add').then(function(data) {
                App.ui.notify('Comment removed!', 'success');
                $this.entryComments = data.result || [];
                $this.refs.comment.value = '';
                $this.commenting = false;
                $this.updateCommentsCount();
                $this.update();
            }).catch(function(e){
                App.ui.notify('Error deleting comment! Try again later.', 'danger');
            });
        });
    }

    this.expandComment = function(e) {
        if ($this.expandedComment !== e.item.comment._id) {
            $this.expandedComment = e.item.comment._id;
            $this.update();
        }
    }

    this.expandReply = function(e) {
        if ($this.expandedComment !== e.item.reply._pid) {
            $this.expandedComment = e.item.reply._pid;
            $this.update();
        }
    }

    this.cancelEntryComment = function() {
        this.refs.comment.value = '';
        this.commenting = false;
        this.update();
    }

    this.cancelCommentReply = function(e) {
        $this.refs["reply" + e.item.comment._id].value = '';
        window.setTimeout(function() {
            $this.expandedComment = false;
            $this.update();
        }, 50);
    }

</script>
