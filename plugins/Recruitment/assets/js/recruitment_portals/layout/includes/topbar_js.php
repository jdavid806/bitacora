<script type="text/javascript">
    //close navbar collapse panel on clicking outside of the panel
    $(document).on('click', function (e) {

        "use strict";  

        if (!$(e.target).is('#navbar') && isMobile()) {
            $('#navbar').collapse('hide');
        }
    });


    $(document).ready(function () {
        "use strict";  
        
        //load message notifications
        var notificationOptions = {};
        var messageOptions = {},
        messageIcon = "#message-notification-icon",
        notificationIcon = "#web-notification-icon";

        //check message notifications
        messageOptions.notificationUrl = "<?php echo_uri('messages/count_notifications'); ?>";
        messageOptions.notificationStatusUpdateUrl = "<?php echo_uri('messages/update_notification_checking_status'); ?>";
        messageOptions.checkNotificationAfterEvery = "<?php echo get_setting('check_notification_after_every'); ?>";
        messageOptions.icon = "mail";
        messageOptions.notificationSelector = $(messageIcon);
        messageOptions.isMessageNotification = true;

        checkNotifications(messageOptions);

        window.updateLastMessageCheckingStatus = function () {
            checkNotifications(messageOptions, true);
        };

        $('body').on('show.bs.dropdown', messageIcon, function () {
            messageOptions.notificationUrl = "<?php echo_uri('messages/get_notifications'); ?>";
            checkNotifications(messageOptions, true);
        });




        //check web notifications
        notificationOptions.notificationUrl = "<?php echo_uri('notifications/count_notifications'); ?>";
        notificationOptions.notificationStatusUpdateUrl = "<?php echo_uri('notifications/update_notification_checking_status'); ?>";
        notificationOptions.checkNotificationAfterEvery = "<?php echo get_setting('check_notification_after_every'); ?>";
        notificationOptions.icon = "bell";
        notificationOptions.notificationSelector = $(notificationIcon);
        notificationOptions.notificationType = "web";


        checkNotifications(notificationOptions); //start checking notification after starting the message checking 

        if (isMobile()) {
            //for mobile devices, load the notifications list with the page load
            notificationOptions.notificationUrlForMobile = "<?php echo_uri('notifications/get_notifications'); ?>";
            checkNotifications(notificationOptions);
        }

        $('body').on('show.bs.dropdown', notificationIcon, function () {
            notificationOptions.notificationUrl = "<?php echo_uri('notifications/get_notifications'); ?>";
            checkNotifications(notificationOptions, true);
        });

        $('[data-bs-toggle="tooltip"]').tooltip();
    });

</script>