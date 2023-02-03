<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<script>
    var chat_api_key = '<?php echo FatApp::getConfig('CONF_COMET_CHAT_API_KEY'); ?>';
    var chat_appid = '<?php echo FatApp::getConfig('CONF_COMET_CHAT_APP_ID'); ?>';
    var CometJsonTeacherData = [{"userId": "<?php echo $teacherDetails['user_id']; ?>", "fname": "<?php echo $teacherDetails['user_first_name']; ?>", "role": "<?php echo User::getUserTypesArr()[User::USER_TYPE_TEACHER]; ?>"}];
    var CometJsonLearnerData = [{"userId": "<?php echo $userDetails['user_id']; ?>", "fname": "<?php echo $userDetails['user_first_name']; ?>", "role": "<?php echo User::getUserTypesArr()[User::USER_TYPE_LEANER]; ?>"}];
    var CometJsonData = CometJsonTeacherData.concat(CometJsonLearnerData);
    var CometJsonFriendData = {"userId": "<?php echo $teacherDetails['user_id']; ?>", "friendId": "<?php echo $userDetails['user_id']; ?>"};
    var chat_group_id = '<?php echo "Chat-" . $teacherDetails['user_id'] . "-" . $userDetails['user_id']; ?>';
    var chat_id = CometJsonFriendData.friendId;
</script>
<section class="section section--grey section--page">
    <div class="container container--fixed">
        <div class="page-panel -clearfix">
            <!--panel left start here-->
            <div class="page-panel__left">
                <?php $this->includeTemplate('account/_partial/dashboardNavigation.php'); ?>
            </div>
            <!--panel left end here-->
            <!--panel right start here-->
            <div class="page-panel__right">
                <div><h1><?php echo Label::getLabel('LBL_Dashboard'); ?></h1></div>
                <div class="screen-chat screen-chat-js">
                    <div class="chat-container">
                        <div id="cometChatBox" class="cometChatBox"></div>
                    </div>
                </div>   
            </div>
            <!--panel right end here-->
        </div>
    </div>
</section>
<div class="gap"></div>
<script>
    $(function () {
        createChatBox = function () {
            var chat_height = '100%';
            var chat_width = '100%';
            $("#cometChatBox").html('<div id="cometchat_embed_synergy_container" style="width:' + chat_width + ';height:' + chat_height + ';max-width:100%;border:1px solid #CCCCCC;border-radius:5px;overflow:hidden;"></div>');
            var chat_js = document.createElement('script');
            chat_js.type = 'text/javascript';
            chat_js.src = 'https://fast.cometondemand.net/' + chat_appid + 'x_xchatx_xcorex_xembedcode.js';
            chat_js.onload = function () {
                var chat_iframe = {};
                chat_iframe.module = "synergy";
                chat_iframe.style = "min-height:" + chat_height + ";min-width:" + chat_width + ";";
                chat_iframe.width = chat_width.replace('px', '');
                chat_iframe.height = chat_height.replace('px', '');
                chat_iframe.src = 'https://' + chat_appid + '.cometondemand.net/cometchat_embedded.php?guid=' + chat_group_id + '&chatroomsonly=1';
                if (typeof (addEmbedIframe) == "function") {
                    addEmbedIframe(chat_iframe);
                }
            }
            var chat_script = document.getElementsByTagName('script')[0];
            chat_script.parentNode.insertBefore(chat_js, chat_script);
        }
        createUserCometChatApi = function (CometJsonData, CometJsonFriendData) {
            $(CometJsonData).each(function (i, val) {
                $.ajax({
                    method: "POST",
                    url: "https://api.cometondemand.net/api/v2/createUser",
                    data: {UID: val.userId, name: val.fname, "role": val.role},
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('api-key', chat_api_key);
                    },
                })
                        .done(function (msg) {
                            if (typeof (msg.success) != "undefined" && msg.success !== null)
                            {
                                $.mbsmessage(msg.success.message, true, 'alert alert--success');
                                location.reload();
                                //addFriendsCometUsers(CometJsonFriendData.userId,CometJsonFriendData.friendId);
                            } else {
                                //$.mbsmessage( msg.failed.message,true, 'alert alert--danger');
                                //addFriendsCometUsers(CometJsonFriendData.userId,CometJsonFriendData.friendId);
                            }
                        });
            });
            createGroup(CometJsonFriendData.friendId, CometJsonFriendData.userId);
        }
        createGroup = function (learnerId, teacherId) {
            $.ajax({
                method: "POST",
                // url: "https://api.cometondemand.net/api/v2/addFriends",
                // data: { UID:learnerId,friendsUID:teacherId},		 
                url: "https://api.cometondemand.net/api/v2/createGroup",
                data: {GUID: chat_group_id, name: chat_group_id, type: 4},
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('api-key', chat_api_key);
                },
            })
                    .done(function (msg) {
                        if (typeof (msg.success) != "undefined" && msg.success !== null)
                        {
                            $.mbsmessage(msg.success.message, true, 'alert alert--success');
                            //createChatBox();
                            //localStorage.setItem('cometChatUserExists',learnerId);
                        } else {
                            // $.mbsmessage( msg.failed.message,true, 'alert alert--danger');
                        }
                    });
            addCometUsersToGroup(learnerId, teacherId);
        }
        addCometUsersToGroup = function (learnerId, teacherId) {
            $.ajax({
                method: "POST",
                url: "https://api.cometondemand.net/api/v2/addUsersToGroup",
                data: {GUID: chat_group_id, UIDs: learnerId + ',' + teacherId},
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('api-key', chat_api_key);
                },
            })
                    .done(function (msg) {
                        if (typeof (msg.success) != "undefined" && msg.success !== null)
                        {
                            $.mbsmessage(msg.success.message, true, 'alert alert--success');
                        } else {
                            // $.mbsmessage( msg.failed.message,true, 'alert alert--danger');
                        }
                    });
            localStorage.setItem('cometPrivateChatUserExists', CometJsonFriendData.friendId);
            createChatBox();
            location.reload();
        }
        addFriendsCometUsers = function (learnerId, teacherId) {
            $.ajax({
                method: "POST",
                url: "https://api.cometondemand.net/api/v2/addFriends",
                data: {UID: learnerId, friendsUID: teacherId},
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('api-key', chat_api_key);
                },
            })
                    .done(function (msg) {
                        if (typeof (msg.success) != "undefined" && msg.success !== null)
                        {
                            //localStorage.setItem('cometChatUserExists',learnerId);
                            $.mbsmessage(msg.success.message, true, 'alert alert--success');
                            // createChatBox();
                        } else {
                            $.mbsmessage(msg.failed.message, true, 'alert alert--danger');
                        }
                    });
        }
    });
    $(document).ready(function () {
        if (localStorage.getItem('cometPrivateChatUserExists') == chat_id)
        {
            createChatBox();
        } else
        {
            createUserCometChatApi(CometJsonData, CometJsonFriendData);
        }
    })
</script>