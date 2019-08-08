<script src="/comment/comment.js" type="text/javascript"></script>
<script src="/comment/md5.js" type="text/javascript"></script>
<?php if (getenv('MOCK_COMMENT')) : ?>
    <script src="/comment/comment-mock.js"></script>
<?php endif; ?>
<style>
    .xblog-comment-form .mdui-textfield {
        padding: 0;
    }

    .xblog-comment-form .xblog-padding-right {
        padding-right: 5px;
    }

    .xblog-comment-form .xblog-padding-left {
        padding-left: 5px;
    }

    .xblog-comment-form .mdui-btn {
        margin-top: 15px;
    }

    .xblog-comment-new {
        border: 1px solid silver;
        padding: 15px 20px;
        margin: 0px;
        border-radius: 7px;
    }

    .xblog-comment-form h2 {
        margin-top: 0.1em;
        margin-bottom: 0.2em;
        margin-left: 0px;
        font-family: inherit;
        font-weight: 400;
        line-height: 1.35;
        color: inherit;
        font-size: 1.5em;
    }

    .xblog-comment .mdui-card {
        margin-bottom: 15px;
    }

    .xblog-no-padding {
        padding: 0 !important;
    }
</style>
<div class="mdui-typo">
    <br />
    <h1>评论</h1>
</div>
<div class="mdui-container-fluid xblog-comment" id="comment">
    <div class="mdui-card">
        <form id="comment_form" class="xblog-comment-form mdui-row mdui-card-content">
            <h2>新评论</h2>
            <div class="xblog-padding-right xblog-xs-no-padding mdui-textfield mdui-textfield-floating-label mdui-col-xs-12 mdui-col-sm-6 mdui-col-md-6 mdui-col-lg-6 mdui-col-xs-6">
                <label class="mdui-textfield-label">昵称</label>
                <input maxlength="25" class="mdui-textfield-input" name="name" type="text" />
            </div>
            <div class="xblog-padding-left xblog-xs-no-padding mdui-textfield mdui-textfield-floating-label mdui-col-xs-12 mdui-col-sm-6 mdui-col-md-6 mdui-col-lg-6 mdui-col-xs-6">
                <label class="mdui-textfield-label">邮箱</label>
                <input maxlength="110" class="mdui-textfield-input" name="email" type="email" />
            </div>
            <div class="mdui-textfield mdui-textfield-floating-label mdui-col-xs-12 mdui-col-sm-12 mdui-col-md-12 mdui-col-lg-12 mdui-col-xs-12">
                <label class="mdui-textfield-label">评论内容……</label>
                <textarea maxlength="350" name="content" class="mdui-textfield-input"></textarea>
            </div>
            <input type="submit" id="comment_submit_button" class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme" value="新评论">
            <button type="button" id="comment_delete_all_button" class="mdui-btn mdui-float-right mdui-ripple">删除所有评论</button>
        </form>
    </div>
    <div id="comment_container"></div>
</div>
<script type="text/plain" id="comment_template">
    <div class="mdui-card" id="comment_id_${id}">
        <div class="mdui-card-header">
            <img class="mdui-card-header-avatar" src="${avatar}" />
            <div class="mdui-card-header-title">${author}</div>
            <div class="mdui-card-header-subtitle">${email}, ${time}</div>
        </div>
        <div class="mdui-card-content">
            <div class="mdui-typo">${content}</div>
        </div>
        <div class="mdui-card-actions">
            <button class="mdui-btn mdui-btn-icon" onclick="window.commentUI.replyTo(${id});">
                <i class="mdui-icon material-icons">reply</i>
            </button>
            <button class="mdui-btn mdui-btn-icon" onclick="window.commentUI.share(${id});">
                <i class="mdui-icon material-icons">share</i>
            </button>
            <button class="mdui-btn mdui-btn-icon mdui-float-right" onclick="window.commentUI.delete(${id});">
                <i class="mdui-icon material-icons">delete_forever</i>
            </button>
        </div>
    </div>
</script>
<script>
    var $$ = mdui.JQ;
    $$('#comment_form').on('submit', () => {
        let form_obj = document.getElementById('comment_form');
        let name = form_obj.name.value;
        let email = form_obj.email.value;
        let content = form_obj.content.value;
        if (!name || !email || !content) {
            mdui.alert('请将表单填写完整！', '提示');
            return false;
        }
        let time = Date.now();
        $$('#comment_submit_button').attr('disabled', 'true');
        commentWorker.create(name, email, content).then((data) => {
            $$('#comment_submit_button').removeAttr('disabled');
            form_obj.content.innerHTML = "";
            form_obj.content.value = "";
            window.appendComment({
                author: name,
                email: email,
                content: content,
                time: time,
                id: data.id
            })
            mdui.alert("评论添加成功！编号：" + data.id.toString(), "提示");
        });
        return false;
    });
    $$('#comment_delete_all_button').on('click', () => {
        window.commentUI.delete(-1);
    });
    window.parseTimeStamp = (time) => {
        var dat = new Date(time);
        var yr = dat.getFullYear().toString();
        var mo = "" + (dat.getMonth() + 1 < 10 ? "0" : "") + (dat.getMonth() + 1);
        var dy = "" + (dat.getDate() < 10 ? "0" : "") + dat.getDate();
        var hr = "" + (dat.getHours() < 10 ? "0" : "") + dat.getHours();
        var mi = "" + (dat.getMinutes() < 10 ? "0" : "") + dat.getMinutes();
        var se = "" + (dat.getSeconds() < 10 ? "0" : "") + dat.getSeconds();
        return yr + "-" + mo + "-" + dy + " " + hr + ":" + mi + ":" + se;
    }
    window.appendComment = (v) => {
        var elemStr = document.getElementById('comment_template').innerHTML;
        elemStr = elemStr.replace(/\${author}/g, v.author);
        elemStr = elemStr.replace(/\${email}/g, v.email);
        v.content = $$('<script type="text/plain">' + v.content + "</script>")[0].innerText;
        elemStr = elemStr.replace(/\${content}/g, v.content);
        elemStr = elemStr.replace(/\${time}/g, window.parseTimeStamp(v.time));
        elemStr = elemStr.replace(/\${avatar}/g, "https://avatar.dawnlab.me/gravatar/" + md5(v.email));
        elemStr = elemStr.replace(/\${id}/g, v.id.toString());
        var elem = $$(elemStr);
        $$("#comment_container").prepend(elem);
    };
    window.commentUI = {
        replyTo: (id) => {
            window.location.hash = "comment";
            $$('#comment_form textarea').prepend('#' + id.toString() + ' ')[0].click();
        },
        share: (id) => {
            var url = location.protocol + "//" + location.host + location.pathname +
                '#comment_id_' + id.toString();
            mdui.alert("本评论链接：<textarea rows=1>" + url + "</textarea>", "分享");
        },
        delete: (id) => {
            mdui.prompt(
                '请输入 Delete Key 以删除 #' + id.toString() + ': ',
                (val) => {
                    var progress_dialog = mdui.dialog({
                        content: '<div class="mdui-progress"><div class="mdui-progress-indeterminate"></div></div>',
                        modal: true
                    });
                    commentWorker.delete(val, id).then((rslt) => {
                        if (rslt.status === 'error') {
                            setTimeout(() => {
                                progress_dialog.close();
                            }, 1000);
                            mdui.alert('删除评论错误：' + rslt.error);
                        } else {
                            window.location.reload();
                        }
                    });
                },
                (val) => {
                    return false;
                }, {
                    type: 'text',
                    confirmText: '确定',
                    cancelText: '取消',
                    modal: true
                }
            );
        }
    };
    commentWorker.get().then((data) => {
        for (var k in data) {
            if (data[k] === null) continue;
            var v = data[k];
            v.id = k;
            window.appendComment(v);
        }
    });
    $$(window).on('load resize', () => {
        if (window.innerWidth <= 600) {
            $$('.xblog-xs-no-padding').addClass('xblog-no-padding');
        } else {
            $$('.xblog-xs-no-padding').removeClass('xblog-no-padding')
        }
    });
</script>
