<link rel="stylesheet" href="<?php echo base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/css/chat.css?v=' . get_setting('app_version')); ?>">
<link rel="stylesheet" href="<?php echo base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/css/lightbox.min.css?v=' . get_setting('app_version')); ?>">

<?php
$csrfToken              = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrfToken;
$allowd_extension       = wbGetAllowedExtension();
?>

<div id="app" class="h-100">
    <div class="container-fluid" id="main-container">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success mb-2 d-flex justify-content-between">
                    <p><?php echo app_lang('chat_message_note'); ?></p>
                    <span id="crossmark" class="hideMessage"><i data-feather='x' class='icon-16'></i></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div v-if="errorMessage" class="alert alert-danger mb-2 d-flex justify-content-between" role="alert">
                    <p>{{ errorMessage }}</p>
                    <span id="crossmark" class="hideMessage"><i data-feather='x' class='icon-16'></i></span>
                </div>
            </div>
        </div>
        <div class="row h-100 ps-3 pe-4">
            <div class="col-12 col-sm-5 col-md-3 d-flex flex-column h-100 border border-1 bg-white" id="chat-list-area" style="position:relative;">
                <!-- Navbar -->
                <div class="row">
                    <div class="d-flex align-items-center p-2 gap-4" id="navbar">
                        <img alt="Profile Photo" class="img-fluid rounded-circle me-2" style="height:50px; width: 50px; cursor:pointer;" id="display-pic" src="<?php echo !empty(get_setting('wb_profile_picture_url')) ? get_setting('wb_profile_picture_url') : base_url('assets/images/avatar.jpg'); ?>">
                        <div class="text-black " id="username" v-if="wb_selectedinteraction && typeof wb_selectedinteraction === 'object'">
                            <?php echo app_lang('from'); ?> {{ wb_selectedinteraction.wa_no }}
                        </div>
                    </div>
                </div>

                <div class="row my-2 gap-2">
                    <div class="col-12">
                        <select class="form-select w-100" v-model="wb_selectedWaNo" v-on:change="wb_filterInteractions" id="wb_selectedWaNo">
                            <option v-for="(interaction, index) in wb_uniqueWaNos" :key="index" :value="interaction.wa_no" :selected="wb_selectedWaNo === 'interaction.wa_no'">{{
                                interaction.wa_no }}</option>
                            <option value="*"><?php echo app_lang('all_chat'); ?></option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="input-group w-100">
                            <input id="wb_searchText" v-model="wb_searchText" type="text" class="form-control" name="wb_searchText" placeholder="Search">
                            <span class="input-group-text" id="search-icon">
                                <i data-feather='search' class='icon-16'></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Chat List -->
                <div class="row" id="chat-list" style="overflow:auto;">
                    <div class="chat-list-item d-flex flex-row w-100 p-2 border-bottom gap-4 align-items-center" v-for="(interaction, index) in wb_displayedInteractions" :key="interaction.id" v-on:click="wb_selectinteraction(interaction.id)" :class="{'selected-interaction': wb_selectedinteraction && wb_selectedinteraction.id === interaction.id}">

                        <p class="img-fluid rounded-circle text-dark fw-semibold d-flex justify-content-center align-items-center my-auto" style="height:50px; width:50px; background-color:#25D366;">
                            {{ wb_getAvatarInitials(interaction.name) }}
                        </p>

                        <div class="w-50">
                            <div class="d-flex justify-content-start align-items-center gap-2">
                                <div class="name fw-semibold">{{ interaction.name }}</div>
                                <span :style="interaction.type === 'leads'
                                        ? { backgroundColor: '#EDE9FE', color: '#5B21B6' }
                                        : interaction.type === 'contacts'
                                        ? { backgroundColor: '#fee2e2', color: '#b91c1c' }
                                        : {}"
                                    class="d-inline-block px-1 rounded"
                                    style="font-size: small;">
                                    {{ interaction.type }}
                                </span>


                            </div>
                            <span class="small last-message" v-html="wb_truncateText(interaction.last_message, 2)"></span>
                        </div>
                        <div class="flex-grow-1 text-right">
                            <div class="small time">{{ wb_formatTime(interaction.time_sent) }}</div>
                            <a href="javascript:void(0)" class="delete dele-icn hide" v-on:click="wb_deleteInteraction(interaction.id)"><i data-feather='trash' class='icon-16 text-danger'></i></a>
                            <span class="badge text-white bg-badge" v-if="wb_countUnreadMessages(interaction.id) > 0">{{
                                wb_countUnreadMessages(interaction.id) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message Area -->
            <div class="d-none d-sm-flex flex-column col-12 col-sm-7 col-md-9 p-0 h-100" id="message-area" v-if="wb_selectedinteraction && typeof wb_selectedinteraction === 'object'">
                <div class="w-100 h-100 overlay d-none"></div>

                <!-- Navbar -->
                <div class="row">
                    <div class="d-flex justify-content-between align-items-center p-2 px-4 " id="navbar">
                        <div class="d-flex align-items-center gap-4">
                            <p class="img-fluid rounded-circle text-dark fw-semibold d-flex justify-content-center align-items-center my-auto" style="height:50px; width:50px; background-color:#25D366;">
                                {{ wb_getAvatarInitials(wb_selectedinteraction.name) }}
                            </p>
                            <div class="d-flex flex-column gap-1">
                                <span class="text-black fw-semibold text-nowrap" id="name">{{ wb_selectedinteraction.name }}</span>
                                <span class="d-flex align-items-center justify-content-start gap-2 fw-semibold text-secondary" id="phonenumber"> <i data-feather='phone' class='icon-16'></i> +{{
                                wb_selectedinteraction.receiver_id
                                }} </span>
                            </div>
                        </div>
                        <?php if ($login_user->is_admin && get_setting('wb_enable_supportagent') == 1) { ?>
                            <div class="d-flex gap-3 align-items-center justify-content-start col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="me-1">
                                        <i class="icon-18 me-2" data-feather="user" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo app_lang('support_agent'); ?>"></i>
                                    </span>
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <div class="d-flex" v-html="wb_selectedinteraction.agent_icon"></div>
                                    </div>
                                </div>
                                <button type="button" v-on:click="wb_initAgent" class="btn btn-secondary btn-sm p-2" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo app_lang('change_support_agent'); ?>">
                                    <i class="icon-18" data-feather="edit"></i>
                                </button>
                            </div>
                        <?php } ?>
                        <span class="small last-message text-black" v-if="wb_selectedinteraction.last_msg_time" v-html="wb_alertTime(wb_selectedinteraction.last_msg_time)">
                        </span>
                    </div>
                </div>

                <?php if ($login_user->is_admin && get_setting('wb_enable_supportagent') == 1) { ?>
                    <div class="modal fade" id="AgentModal" tabindex="-1" aria-labelledby="AgentModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="AgentModalLabel"><?php echo app_lang('modal_title'); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <select name="assigned[]" id="assigned" class="form-control validate-hidden select2" data-rule-required="1" data-msg-required="<?php echo app_lang('field_required'); ?>" tabindex="-1" multiple>
                                        <?php foreach ($staff as $value) { ?>
                                            <option value="<?php echo $value['id']; ?>"><?php echo $value['first_name'] . ' ' . $value['last_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo app_lang('close_btn'); ?></button>
                                    <button type="button" class="btn btn-primary" v-on:click="wb_handleAssignedChange"><?php echo app_lang('save_btn'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="position-relative d-flex justify-content-center">
                    <div class="position-absolute" style="top: 5%; width: 50%;">
                        <div v-if="overdueAlert" v-html="overdueAlert" class="mt-4"></div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="overflow-x-hidden h-100 custom-scrollbar row" style="background-image: url('<?php echo base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/images/bg.png'); ?>')" v-if="wb_selectedinteraction && wb_selectedinteraction.messages" ref="wb_chatContainer">
                    <div v-for="(message, index) in wb_selectedinteraction.messages" :key="index">
                        <div v-if="wb_shouldShowDate(message, wb_selectedinteraction.messages[index - 1])" class="text-center my-3">
                            <span class="px-2 py-1 bg-white font-12 rounded">{{ getDate(message.time_sent) }}</span>
                        </div>
                        <div :class="[message.sender_id === wb_selectedinteraction.wa_no ? 'd-flex justify-content-end mb-3' : 'd-flex mb-3']">
                            <div :class="[
                                    'rounded p-1 ms-2',
                                     message.sender_id === wb_selectedinteraction.wa_no ? 'bg-custom' : 'bg-white bg-opacity-10',
                                     message.staff_id == 0 && message.sender_id === wb_selectedinteraction.wa_no ? 'custom_message' : '' ,
                                     message.type === 'text' && message.message.length > 50 ? 'custom_width' : ''
                                    ]" v-bind="message.sender_id === wb_selectedinteraction.wa_no ? {
                                         'data-bs-toggle': 'tooltip',
                                         'title': message.staff_name,
                                         'data-original-title': message.staff_name,
                                         'data-bs-placement': 'left'
                                       } : {}">

                                <template v-if="message.ref_message_id">
                                    <div class="bg-light rounded-lg mb-2">
                                        <div class="d-flex flex-column gap-2 p-2">
                                            <p class="text-muted fw-normal"><?php echo app_lang('replying_to'); ?></p>
                                            <p class="text-dark" v-html="getOriginalMessage(message.ref_message_id).message"></p>
                                            <div v-if="getOriginalMessage(message.ref_message_id).assets_url">
                                                <template v-if="getOriginalMessage(message.ref_message_id).type === 'image'">
                                                    <a :href="getOriginalMessage(message.ref_message_id).asset_url" data-lightbox="image-group" target="_blank">
                                                        <img :src="getOriginalMessage(message.ref_message_id).asset_url" class="rounded-lg img-fluid" style="max-width: 240px; max-height: 112px;" alt="Image">
                                                    </a>
                                                </template>
                                                <template v-if="getOriginalMessage(message.ref_message_id).type === 'video'">
                                                    <video :src="getOriginalMessage(message.ref_message_id).asset_url" controls class="rounded-lg" style="max-width: 240px; max-height: 112px;"></video>
                                                </template>
                                                <template v-if="getOriginalMessage(message.ref_message_id).type === 'document'">
                                                    <a :href="getOriginalMessage(message.ref_message_id).asset_url" target="_blank" class="text-primary text-decoration-underline"><?php echo app_lang('download_document'); ?></a>
                                                </template>
                                                <template v-if="getOriginalMessage(message.ref_message_id).type === 'audio'">
                                                    <audio controls class="w-100" style="max-width: 250px;">
                                                        <source :src="getOriginalMessage(message.ref_message_id).asset_url" type="audio/mpeg">
                                                    </audio>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <template v-if="message.type === 'text'">
                                    <p class="text-black p-1" :style="message.staff_id == 0 && message.sender_id === wb_selectedinteraction.wa_no ? '' :{ whiteSpace: 'pre-wrap' }" v-html="formatMessage(message.message)"></p>
                                </template>
                                <template v-if="message.type === 'interactive'">
                                    <p class="text-black">{{ message.message }}</p>
                                </template>

                                <template v-if="message.type === 'button'">
                                    <p class="text-black" v-html="message.message"></p>
                                </template>

                                <template v-if="message.type === 'reaction'">
                                    <p class="text-black" v-html="message.message"></p>
                                </template>

                                <template v-else-if="message.type === 'image'">
                                    <a :href="message.asset_url" data-lightbox="image-group">
                                        <img :src="message.asset_url" alt="Image" class="img-fluid rounded" style="max-width: 200px; max-height: 112px;">
                                    </a>
                                    <p class="small mt-2" v-if="message.caption">{{ message.caption }}</p>
                                </template>

                                <template v-else-if="message.type === 'video'">
                                    <video :src="message.asset_url" controls class="img-fluid rounded" style="max-width: 200px; max-height: 112px;"></video>
                                    <p class="small mt-2" v-if="message.message">{{ message.message }}</p>
                                </template>

                                <template v-else-if="message.type === 'document'">
                                    <a :href="message.asset_url" target="_blank" class="text-primary"><?php echo app_lang('download_document'); ?></a>
                                </template>

                                <template v-else-if="message.type === 'audio'">
                                    <audio :src="message.asset_url" controls style="max-width: 200px;"></audio>
                                    <p class="small mt-2" v-if="message.message">{{ message.message }}</p>
                                </template>

                                <div class="d-flex justify-content-between gap-4 align-items-center mt-0">
                                    <span class='text-muted'>{{
                                        wb_getTime(message.time_sent) }}
                                    </span>
                                    <div>
                                        <span v-on:click="replyToMessage(message)" style="cursor: pointer;">
                                            <i data-feather='corner-up-left' class="icon-16"></i>
                                        </span>
                                        <span class="ms-2 " v-if="message.sender_id === wb_selectedinteraction.wa_no">
                                            <i v-if="message.status === 'sent'" data-feather='check' class='icon-16 text-muted' title="Sent"></i>
                                            <img v-else-if="message.status === 'delivered'" src="<?php echo base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/images/delivered.png'); ?>" class="icon-16" title="Delivered" alt="Delivered">
                                            <img v-else-if="message.status === 'read'" src="<?php echo base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/images/double-check-read.png'); ?>" class='icon-16' title="Read" alt="Read">
                                            <i v-else-if="message.status === 'failed'" data-feather='alert-circle' class='icon-16 text-warning' title="Failed"></i>
                                            <i v-else-if="message.status === 'deleted'" data-feather='trash' class='icon-16 text-danger' title="Deleted"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="replyingToMessage" class=" d-flex justify-content-center">
                    <div class=" w-100 bg-light rounded">
                        <div class="bg-white w-100 p-4 rounded d-flex justify-content-between align-items-center">
                            <div class="d-flex flex-column gap-2">
                                <p class="text-muted fw-normal"><?php echo app_lang('replying_to'); ?></p>
                                <p class="text-dark " v-html="replyingToMessage.message"></p>
                                <div v-if="replyingToMessage.asset_url">
                                    <template v-if="replyingToMessage.type === 'image'">
                                        <img :src="replyingToMessage.asset_url" class="rounded-lg img-fluid" style="max-width: 240px; max-height: 112px;" alt="Image">
                                    </template>
                                    <template v-if="replyingToMessage.type === 'video'">
                                        <video :src="replyingToMessage.asset_url" controls class="rounded-lg" style="max-width: 240px; max-height: 112px;"></video>
                                    </template>
                                    <template v-if="replyingToMessage.type === 'document'">
                                        <a :href="replyingToMessage.asset_url" target="_blank" class="text-primary text-decoration-underline"><?php echo app_lang('download_document'); ?></a>
                                    </template>
                                    <template v-if="replyingToMessage.type === 'audio'">
                                        <audio controls class="w-100" style="max-width: 250px;">
                                            <source :src="replyingToMessage.asset_url" type="audio/mpeg">
                                        </audio>
                                    </template>
                                </div>
                            </div>
                            <button v-on:click="clearReply" class="btn p-0 text-danger">
                                &#10006;
                            </button>
                        </div>
                        <ul v-if="showQuickReplies" class="flex-grow bg-white shadow-md rounded-lg mt-2 p-2 list-unstyled">
                            <li v-for="(reply, index) in filteredQuickReplies"
                                :key="index"
                                v-on:click="selectQuickReply(index)"
                                :class="{
                                            'bg-primary text-white': index === quickReplyIndex,
                                            'hover:bg-light cursor-pointer rounded p-2 transition-all': true
                                        }">
                            </li>
                        </ul>
                    </div>
                </div>

                <div v-if="wb_imageAttachment || wb_videoAttachment || wb_documentAttachment" class="d-flex flex-wrap gap-3 p-4">
                    <!-- Image Attachment -->
                    <div v-if="wb_imageAttachment" class="position-relative d-flex flex-column align-items-center py-6 px-4 bg-light rounded-lg shadow-lg" style="max-width: 250px;">
                        <!-- Preview Text -->
                        <div class="d-flex align-items-center gap-4">
                            <span class="text-xs fw-semibold text-muted mb-2"><?php echo app_lang('preview'); ?></span>
                            <button v-on:click="wb_removeImageAttachment" class="btn position-absolute top-0 end-0 text-danger mt-1 ">
                                &#10006;
                            </button>
                        </div>
                        <img :src="wb_imagePreview" alt="Selected Image" class="w-100 h-100 object-cover rounded mb-3 " />
                        <span class="mt-2 text-sm fw-medium text-body truncate w-100 text-center">{{ wb_imageAttachment.name }}</span>
                    </div>

                    <!-- Video Attachment -->
                    <div v-if="wb_videoAttachment" class="position-relative d-flex flex-column align-items-center py-4 px-4 bg-light rounded-lg shadow-lg" style="max-width: 280px;">
                        <!-- Preview Text -->
                        <div class="d-flex align-items-center gap-4">
                            <span class="text-xs fw-semibold text-muted mb-2"><?php echo app_lang('preview'); ?></span>
                            <button v-on:click="wb_removeVideoAttachment" class="btn position-absolute top-0 end-0 text-danger ">
                                &#10006;
                            </button>
                        </div>
                        <video :src="wb_videoPreview" controls class="w-100 object-cover rounded"></video>
                        <span class="mt-2 text-sm text-body truncate w-100 text-center">{{ wb_videoAttachment.name }}</span>
                    </div>

                    <!-- Document Attachment -->
                    <div v-if="wb_documentAttachment" class="position-relative d-flex flex-column align-items-center p-2 bg-light rounded-lg shadow-lg" style="max-width: 250px; min-width: 200px;">
                        <!-- Preview Text -->
                        <div class="d-flex align-items-center gap-4">
                            <span class="text-xs fw-semibold text-muted mb-2"><?php echo app_lang('preview'); ?></span>
                            <button v-on:click="wb_removeDocumentAttachment" class="btn position-absolute top-0 end-0 text-danger ">
                                &#10006;
                            </button>
                        </div>
                        <span class="mt-2 text-sm text-body truncate w-100 text-center">{{ wb_documentAttachment.name }}</span>
                    </div>
                </div>

                <!-- Input -->
                <form @submit.prevent="wb_sendMessage" class="relative">
                    <div class="w-100 bg-light rounded-lg px-4 pt-4 text-sm">
                        <textarea v-model="wb_newMessage" ref="inputField" class="mentionable custom-textarea" rows="2" placeholder="<?= app_lang('type_your_message') ?>" id="wb_newMessage" @keyup.enter="handleKeyPress"></textarea>
                    </div>
                    <div class="justify-content-between align-items-center d-flex py-2 px-2 bg-light" id="input-area">
                        <div class="d-flex align-items-center">

                            <?php if (get_setting('enable_wb_openai')) { ?>
                                <div class="dropup">
                                    <button id="ai-dropdown" type="button" class="btn p-0 m-0 nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false" :class="{ 'disabled': !isButtonEnabled }">
                                        <span tabindex="0" data-bs-toggle="tooltip" title="<?php echo app_lang('ai_prompt_note'); ?>">
                                            <img src="<?= base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/images/ai.png') ?>" class="icon-18 mx-2 mb-1">
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <span class="ms-2"><img src="<?= base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/images/ai.png') ?>" class="icon-18 mx-2 mb-1"><?= app_lang('ai_prompt') ?></span>
                                        <li role="separator" class="dropdown-divider"></li>
                                        <!-- Change Tone Submenu -->
                                        <li class="dropdown-submenu">
                                            <a id="change-tone-dropdown" href="javascript:;" class="dropdown-item">
                                                <i class="icon-16 me-2 text-info" data-feather="headphones"></i><?= app_lang('change_tone') ?>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li v-on:click="wb_handleItemClick('<?= app_lang('change_tone') ?>', '<?= app_lang('professional') ?>')">
                                                    <a href="javascript:;" class="dropdown-item"><?= app_lang('professional') ?></a>
                                                </li>
                                                <li v-on:click="wb_handleItemClick('<?= app_lang('change_tone') ?>', '<?= app_lang('friendly') ?>')">
                                                    <a href="javascript:;" class="dropdown-item"><?= app_lang('friendly') ?></a>
                                                </li>
                                                <li v-on:click="wb_handleItemClick('<?= app_lang('change_tone') ?>', '<?= app_lang('empathetic') ?>')">
                                                    <a href="javascript:;" class="dropdown-item"><?= app_lang('empathetic') ?></a>
                                                </li>
                                                <li v-on:click="wb_handleItemClick('<?= app_lang('change_tone') ?>', '<?= app_lang('straightforward') ?>')">
                                                    <a href="javascript:;" class="dropdown-item"><?= app_lang('straightforward') ?></a>
                                                </li>
                                            </ul>
                                        </li>

                                        <!-- Translate Submenu -->
                                        <li class="dropdown-submenu dropup">
                                            <a href="javascript:;" class="dropdown-item">
                                                <i class="icon-16 me-2 text-info" data-feather="activity"></i><?= app_lang('translate') ?>
                                            </a>
                                            <ul class="dropdown-menu custom-dropdown-menu">
                                                <li>
                                                    <input type="text" class="form-control" style="border-radius: 25px;" v-model="searchQuery" placeholder="<?= app_lang('search_language') ?>" />
                                                </li>
                                                <li v-for="lang in filteredLanguages" :key="lang">
                                                    <a href="javascript:;" class="dropdown-item" v-on:click="wb_handleItemClick('Translate', lang)">
                                                        {{ ucfirst(lang) }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li v-on:click="wb_handleItemClick('<?php echo app_lang('fix_spelling_and_grammar'); ?>')">
                                            <a href="javascript:;" class="dropdown-item"><i class="icon-16 me-2 text-info" data-feather="check"></i><?php echo app_lang('fix_spelling_and_grammar'); ?></a>
                                        </li>

                                        <li v-on:click="wb_handleItemClick('<?php echo app_lang('simplify_language'); ?>')">
                                            <a href="javascript:;" class="dropdown-item"><i class="icon-16 me-2 text-info" data-feather="disc"></i><?php echo app_lang('simplify_language'); ?></a>
                                        </li>

                                        <li class="dropdown-submenu dropup" v-if="customPrompts.length > 0">
                                            <a href="javascript:;" class="dropdown-item"><i class="icon-16 me-2 text-info" data-feather="corner-up-left"></i><?= app_lang('custom_prompt') ?></a>
                                            <ul class="dropdown-menu custom-dropdown-menu">
                                                <li v-for="(prompt, index) in customPrompts" :key="index" v-if="shouldDisplayPrompt(prompt)" v-on:click="wb_handleItemClick('<?php echo app_lang('custom_prompt'); ?>', prompt.action)">
                                                    <a href="javascript:;" class="dropdown-item">{{ prompt.label }}</a>
                                                </li>
                                            </ul>
                                        </li>

                                    </ul>
                                </div>
                            <?php } ?>

                            <span class="btn" v-on:click="toggleEmojiPicker" id="emoji_btn" data-bs-toggle="tooltip" title="<?php echo app_lang('emojis'); ?>" data-bs-placement="top" data-container="body">
                                <i class="icon-18 me-2" data-feather="smile"></i>
                            </span>

                            <button v-on:click="toggleAttachmentOptions" type="button" class="btn p-0 m-0" data-bs-toggle="tooltip" title="<?php echo app_lang('attach_image_video_docs'); ?>" data-bs-placement="top">
                                <i data-feather="paperclip" class="icon-18 mx-2"></i>
                            </button>

                            <button v-on:click="wb_toggleRecording" type="button" class="btn p-0 ml10" data-bs-toggle="tooltip" title="<?php echo app_lang('record_audio'); ?>" data-bs-placement="top">
                                <img v-if="!wb_recording" src="<?= base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/images/microphone.png') ?>" class="icon-18 mx-2" alt="mic">
                                <img v-else src="<?= base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/images/mute.png') ?>" class="icon-18 mx-2" alt="mic-off">
                            </button>
                        </div>
                        <div class="d-flex align-items-center gap-4">
                            <div class="text-sm text-muted fw-semibold"><?php echo app_lang('use_@_to_add_merge_fields'); ?></div>
                            <button v-if="wb_showSendButton" type="submit" class="rounded bg-light  border-0 m-0 pt-2">
                                <img src="<?= base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/images/send.png') ?>" class="icon-18 mx-2 mb-1" title="Send" alt="send">
                            </button>
                        </div>
                        <div class="position-absolute" style="bottom: -14px; left:495px;">
                            <!-- Attachment Options Dropdown (conditionally visible) -->
                            <div v-if="showAttachmentOptions" class="d-flex flex-column gap-2 text-nowrap bg-light shadow-lg rounded p-2">

                                <input type="file" id="imageAttachmentInput" ref="imageAttachmentInput" v-on:change="wb_handleImageAttachmentChange" accept="<?php echo $allowd_extension['image']['extension']; ?>" class="d-none">
                                <label for="imageAttachmentInput" class="pointer d-flex align-items-center p-2" style="cursor: pointer;"
                                    data-bs-toggle="tooltip" title="<?php echo app_lang('send_image'); ?>" data-bs-placement="top">
                                    <img src="<?= base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/images/icon_ImageMessage.png') ?>" class="icon-18 mx-2"><span><?= app_lang('send_image') ?></span>
                                </label>

                                <input type="file" id="videoAttachmentInput" ref="videoAttachmentInput" v-on:change="wb_handleVideoAttachmentChange" accept="<?php echo $allowd_extension['video']['extension']; ?>" class="d-none">
                                <label for="videoAttachmentInput" class="pointer d-flex align-items-center p-2" style="cursor: pointer;"
                                    data-bs-toggle="tooltip" title="<?php echo app_lang('send_video'); ?>" data-bs-placement="top">
                                    <img src="<?= base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/images/icon_VideoMessage.png') ?>" class="icon-18 mx-2"><span><?= app_lang('send_video') ?></span>
                                </label>

                                <input type="file" id="documentAttachmentInput" ref="documentAttachmentInput" v-on:change="wb_handleDocumentAttachmentChange" accept="<?php echo $allowd_extension['document']['extension']; ?>" class="d-none">
                                <label for="documentAttachmentInput" class="pointer d-flex align-items-center p-2" style="cursor: pointer;"
                                    data-bs-toggle="tooltip" title="<?php echo app_lang('send_document'); ?>" data-bs-placement="top">
                                    <img src="<?= base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/images/icon_Document.png') ?>" class="icon-18 mx-2"><span><?= app_lang('send_document') ?></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="emoji-picker-container" ref="emojiPickerContainer"></div>
                    <input type="hidden" name="rel_type" id="rel_type" value="">
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/js/vue.min.js?v=' . get_setting('app_version')); ?>"></script>
<script src="<?php echo base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/js/axios.min.js?v=' . get_setting('app_version')); ?>"></script>
<script src="<?php echo base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/js/purify.min.js?v=' . get_setting('app_version')); ?>"></script>
<script src="<?php echo base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/js/recorder-core.js?v=' . get_setting('app_version')); ?>"></script>
<script src="<?php echo base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/js/lightbox.min.js?v=' . get_setting('app_version')); ?>"></script>
<script src="<?php echo base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/js/mp3.js?v=' . get_setting('app_version')); ?>"></script>
<script src="<?php echo base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/js/mp3-engine.js?v=' . get_setting('app_version')); ?>"></script>
<script src="<?php echo base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/js/emoji-mart.min.js?v=' . get_setting('app_version')); ?>"></script>

<script>
    "use strict";
    $(document).on('click', '.hideMessage', function() {
        $(this).parent().addClass('hide');
    });

    $(function() {
        $('#js-init-chat-icon').removeClass('init-chat-icon');
        $('[data-bs-toggle="tooltip"]').tooltip();

        $(document).on('mouseenter', '.chat-list-item', function() {
            $(this).find('.dele-icn').removeClass('hide');
        });

        $(document).on('mouseleave', '.chat-list-item', function() {
            $(this).find('.dele-icn').addClass('hide');
        });
    })

    var default_whatsapp_number = <?php echo get_setting('wb_default_phone_number'); ?>;

    new Vue({
        el: '#app',
        data() {
            return {
                interactions: [],
                previousCounts: {},
                wb_selectedinteractionIndex: null,
                wb_selectedinteraction: null,
                wb_selectedinteractionMobNo: null,
                wb_selectedinteractionSenderNo: null,
                wb_selectedinteractionId: null,
                wb_newMessage: '',
                wb_imageAttachment: null,
                wb_videoAttachment: null,
                wb_documentAttachment: null,
                wb_imagePreview: '',
                wb_videoPreview: '',
                wb_csrfToken: '<?php echo $csrfToken; ?>',
                wb_recording: false,
                wb_audioBlob: null,
                wb_recordedAudio: null,
                errorMessage: '',
                wb_searchText: '',
                showAttachmentOptions: false,
                wb_selectedWaNo: '<?php echo get_setting('wb_default_phone_number'); ?>', // Define selectedWaNo variable
                wb_filteredInteractions: [], // Define wb_filteredInteractions to store filtered interactions
                wb_displayedInteractions: [],
                languages: <?= json_encode($languages->languages) ?>,
                searchQuery: '',
                wb_agentId: '',
                wb_login_staff_id: '<?= $_SESSION['user_id'] ?>',
                has_pemission_view_ai_prompts: '<?= check_wb_permission($login_user, 'wb_view_ai_prompts') ?>',
                replyingToMessage: null,
                showQuickReplies: false,
                customPrompts: [],
            };
        },

        methods: {
            formatMessage(text) {
                const patterns = [
                    /\*(.*?)\*/g, // Bold
                    /_(.*?)_/g, // Italic
                    /~(.*?)~/g, // Strikethrough
                    /```(.*?)```/g // Monospace
                ];

                const replacements = [
                    '<strong>$1</strong>',
                    '<em>$1</em>',
                    '<del>$1</del>',
                    '<code>$1</code>'
                ];

                for (let i = 0; i < patterns.length; i++) {
                    text = text.replace(patterns[i], replacements[i]);
                }

                return text;
            },
            handleKeyPress(event) {
                if (event.keyCode == 13 && !event.shiftKey) {
                    event.preventDefault();
                    if (this.wb_newMessage.trim() != '') {
                        this.wb_sendMessage();
                        return true;
                    }
                }
            },
            wb_fetchCustomPrompts() {
                $.ajax({
                    url: '<?php echo get_uri('whatsboost/get_prompts'); ?>',
                    type: 'POST',
                    dataType: 'json',
                    success: (response) => {
                        if (Array.isArray(response.custom_prompts)) {
                            this.customPrompts = response.custom_prompts.map(prompt => ({
                                label: prompt.name,
                                action: prompt.action,
                                added_from: prompt.added_from

                            }));
                        } else {
                            console.error('Invalid response structure', response);
                        }
                    },
                    error: (error) => {
                        console.error('Error fetching AI Prompts', error);
                    }
                });
            },
            shouldDisplayPrompt(prompt) {
                return this.wb_login_staff_id === prompt.added_from || this.has_pemission_view_ai_prompts;
            },
            wb_selectinteraction(id) {
                $.ajax({
                    url: '<?php echo get_uri('whatsboost/chat_mark_as_read'); ?>',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        'interaction_id': id
                    },
                })
                const index = this.interactions.findIndex(interaction => interaction.id === id);
                if (index !== -1) {
                    this.wb_selectedinteractionIndex = index;
                    this.wb_selectedinteraction = this.interactions[index];
                    this.wb_selectedinteractionId = this.wb_selectedinteraction['id'];
                    this.wb_selectedinteractionMobNo = this.wb_selectedinteraction['receiver_id'];
                    this.wb_selectedinteractionSenderNo = this.wb_selectedinteraction['wa_no'];
                    this.wb_scrollToBottom();
                    this.wb_fetchCustomPrompts();
                    this.$nextTick(() => {
                        $('[data-bs-toggle="tooltip"]').tooltip();
                        $('#rel_type').val(this.wb_selectedInteraction['type']);
                        $('#rel_type').trigger('change');
                    });
                }
            },

            wb_deleteInteraction(id) {
                const result = confirm("Are you sure you want to delete this chat?");
                if (result) {
                    $.ajax({
                        url: '<?php echo get_uri('whatsboost/delete_chat'); ?>',
                        type: 'POST',
                        dataType: 'html',
                        data: {
                            'interaction_id': id
                        },
                    })
                }
            },
            toggleAttachmentOptions() {
                this.showAttachmentOptions = !this.showAttachmentOptions;
            },
            async wb_sendMessage() {
                if (!this.wb_selectedinteraction) return;
                if (default_whatsapp_number != this.wb_selectedinteraction['wa_no']) {
                    this.errorMessage = 'Default WhatsApp number does not match the selected interaction.';
                    return;
                }
                const wb_formData = new FormData();
                wb_formData.append('id', this.wb_selectedinteraction.id);
                wb_formData.append('to', this.wb_selectedinteraction.receiver_id);
                wb_formData.append('csrf_token_name', this.wb_csrfToken);
                wb_formData.append('type', this.wb_selectedinteraction.type);
                wb_formData.append('type_id', this.wb_selectedinteraction.type_id);

                const MAX_MESSAGE_LENGTH = 2000;
                if (this.wb_newMessage.length > MAX_MESSAGE_LENGTH) {
                    this.wb_newMessage = this.wb_newMessage.substring(0, MAX_MESSAGE_LENGTH);

                }
                // Add message if it exists
                if (this.wb_newMessage.trim()) {
                    wb_formData.append('message', DOMPurify.sanitize(this.wb_newMessage));
                }

                // Handle image attachment
                if (this.wb_imageAttachment) {
                    wb_formData.append('image', this.wb_imageAttachment);
                }

                // Handle video attachment
                if (this.wb_videoAttachment) {
                    wb_formData.append('video', this.wb_videoAttachment);
                }

                // Handle document attachment
                if (this.wb_documentAttachment) {
                    wb_formData.append('document', this.wb_documentAttachment);
                }

                // Handle audio attachment
                if (this.wb_audioBlob) {
                    wb_formData.append('audio', this.wb_audioBlob, 'audio.mp3');
                }
                if (this.replyingToMessage) {
                    wb_formData.append('ref_message_id', this.replyingToMessage.message_id);
                }
                try {
                    const wb_response = await axios.post('<?php echo get_uri('whatsboost/send_message'); ?>', wb_formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });

                    // Clear attachments
                    this.wb_newMessage = '';
                    this.wb_imageAttachment = null;
                    this.wb_videoAttachment = null;
                    this.wb_documentAttachment = null;
                    this.wb_audioBlob = null;
                    this.wb_imagePreview = '';
                    this.wb_videoPreview = '';
                    this.wb_filterInteractions();
                    this.wb_selectinteraction(this.wb_selectedinteraction.id);
                    this.errorMessage = '';
                    this.clearReply();
                    this.wb_scrollToBottom();
                    this.wb_selectedinteractionIndex = 0;
                } catch (error) {
                    const wb_rawErrorMessage = error.response && error.response.data ? error.response.data : 'An error occurred. Please try again.';
                    // Define regular expressions to match the desired parts of the HTML error message
                    const wb_typeRegex = /<p>Type: (.+)<\/p>/;
                    const wb_messageRegex = /<p>Message: (.+)<\/p>/;

                    // Extract the type and message from the HTML error message
                    const wb_typeMatch = wb_rawErrorMessage.match(wb_typeRegex);
                    var wb_messageMatch = wb_rawErrorMessage.match(wb_messageRegex);

                    if (typeof(wb_messageMatch[1] == 'object')) {
                        wb_messageMatch[1] = JSON.parse(wb_messageMatch[1]);
                        wb_messageMatch[1] = wb_messageMatch[1].error.message;
                    }

                    const wb_getTypeText = wb_typeMatch ? wb_typeMatch[1] : '';
                    const wb_getMessageText = wb_messageMatch ? wb_messageMatch[1] : '';

                    // Construct the error message by concatenating the extracted text content
                    const errorMessage = wb_getTypeText.trim() + '\n' + wb_getMessageText.trim();
                    this.errorMessage = errorMessage;
                }
            },
            replyToMessage(message) {
                this.replyingToMessage = message || message.asset_url;
                this.wb_scrollToBottom();
            },
            clearReply() {
                this.replyingToMessage = null;
            },
            wb_initAgent() {
                const agentId = this.wb_selectedinteraction.agent.agent_id;
                this.selectedStaffId = agentId;
                $('#AgentModal').modal('show');
                $('#AgentModal').find('select[name="assigned[]"]').val(agentId);
                $('#AgentModal').find('select[name="assigned[]"]').select2();
            },

            wb_handleAssignedChange(event) {
                const id = this.wb_selectedinteraction ? this.wb_selectedinteraction.id : null;
                const staffId = $('select[name="assigned[]"]').val();
                $.ajax({
                    url: '<?= get_uri('whatsboost/assign_staff') ?>',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        'staff_id': staffId,
                        'interaction_id': id
                    },
                })
                this.wb_selectinteraction(id);
                $('#AgentModal').modal('hide');
            },

            sanitizeMessage(message) {
                return DOMPurify.sanitize(message, {
                    USE_PROFILES: {
                        html: true
                    }
                });
            },

            trimMessage(message, maxLength = 100) {
                const sanitizedMessage = this.sanitizeMessage(message);
                if (sanitizedMessage.length > maxLength) {
                    return sanitizedMessage.substring(0, maxLength) + '...';
                }
                return sanitizedMessage;
            },

            getOriginalMessage(refMessageId) {
                const message = this.wb_selectedinteraction.messages.find(msg => msg.message_id === refMessageId) || {};
                return {
                    ...message,
                    message: this.trimMessage(message.message),
                    assets_url: message.asset_url || ''
                };
            },
            wb_clearMessage() {
                this.wb_newMessage = '';
                this.attachment = null;
                this.wb_audioBlob = null;
            },

            wb_handleAttachmentChange(event) {
                const files = event.target.files;
                this.attachment = files[0];
            },

            async wb_fetchinteractions() {
                try {
                    const staff_id = this.wb_login_staff_id;
                    const wb_response = await fetch('<?php echo get_uri('whatsboost/interactions'); ?>');
                    const data = await wb_response.json();
                    const enable_supportagent = "<?= get_setting('wb_enable_supportagent') ?>";

                    if (data && data.interactions) {

                        const isAdmin = <?php echo $login_user->is_admin ? 'true' : 'false'; ?>;

                        if (!isAdmin && enable_supportagent == 1) {
                            this.interactions = data.interactions.filter(interaction => {

                                const chatagent = interaction.agent;
                                if (chatagent) {
                                    const agentIds = Array.isArray(chatagent.agent_id) ? chatagent.agent_id : [chatagent.agent_id];
                                    const assignIds = Array.isArray(chatagent.assign_id) ? chatagent.assign_id : [chatagent.assign_id];
                                    // Check if `staff_id` is included in either `agentIds` or `assignIds`
                                    return agentIds.includes(staff_id) || assignIds.includes(staff_id);
                                }
                                return false;
                            });
                        } else {
                            this.interactions = data.interactions;
                        }
                    } else {
                        this.interactions = [];
                    }

                    this.wb_filterInteractions();
                    this.wb_updateSelectedInteraction();
                } catch (error) {
                    console.error('Error fetching interactions:', error);
                }
            },

            wb_updateSelectedInteraction() {
                const wb_new_index = this.interactions.findIndex(interaction => interaction.receiver_id === this.wb_selectedinteractionMobNo && interaction.wa_no === this.wb_selectedinteractionSenderNo && interaction.id === this.wb_selectedinteractionId);
                this.wb_selectedinteraction = this.interactions[wb_new_index];
            },

            wb_getTime(timeString) {
                const date = new Date(timeString);
                const hour = date.getHours();
                const minute = date.getMinutes();
                const period = hour < 12 ? 'AM' : 'PM';
                const formattedHour = hour % 12 || 12;
                return `${formattedHour}:${minute < 10 ? '0' + minute : minute} ${period}`;
            },
            getDate(dateString) {
                const wb_date = new Date(dateString);
                const wb_options = {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                };
                return wb_date.toLocaleDateString('en-GB', wb_options).replace(' ', '-').replace(' ', '-');
            },

            wb_shouldShowDate(currentMessage, previousMessage) {
                if (!previousMessage) return true;
                return this.getDate(currentMessage.time_sent) !== this.getDate(previousMessage.time_sent);
            },

            wb_scrollToBottom() {
                this.$nextTick(() => {
                    $('select[name="assigned[]"]').select2();
                    const wb_chatContainer = this.$refs.wb_chatContainer;
                    if (wb_chatContainer) {
                        wb_chatContainer.scrollTop = wb_chatContainer.scrollHeight;
                    }
                });
            },

            wb_getAvatarInitials(name) {
                const wb_words = name.split(' ');
                const wb_initials = wb_words.slice(0, 2).map(word => word.charAt(0)).join('');
                return wb_initials.toUpperCase();
            },

            playNotificationSound() {
                var enableSound = "<?= get_setting('wb_enable_notification_sound') ?>";

                if (enableSound == 1) {
                    var audio = new Audio('<?= base_url(PLUGIN_URL_PATH . 'WhatsBoost/assets/audio/whatsapp_notification.mp3'); ?>');
                    audio.play();
                }
            },

            wb_countUnreadMessages(interactionId) {
                const interaction = this.interactions.find(inter => inter.id === interactionId);
                if (interaction) {
                    return interaction.messages.filter(message => message.is_read == 0).length;
                }
                return 0;
            },

            async wb_toggleRecording() {
                if (!this.wb_recording) {
                    // Start wb_recording
                    this.wb_startRecording();
                } else {
                    // Stop wb_recording
                    this.wb_stopRecording();
                }
            },
            wb_startRecording() {
                // Initialize Recorder.js if not already initialized
                if (!this.recorder) {
                    this.recorder = new Recorder({
                        type: "mp3",
                        sampleRate: 16000,
                        bitRate: 16,
                        onProcess: (buffers, powerLevel, bufferDuration, bufferSampleRate) => {

                        }
                    });
                }
                this.recorder.open((stream) => {
                    this.wb_recording = true;
                    this.recorder.start();
                }, (err) => {
                    console.error("Failed to start wb_recording:", err);
                });
            },

            wb_stopRecording() {
                if (this.recorder && this.wb_recording) {
                    this.recorder.stop((blob, duration) => {
                        this.recorder.close();
                        this.wb_recording = false;
                        this.wb_audioBlob = blob;
                        this.wb_sendMessage();
                        this.wb_recordedAudio = URL.createObjectURL(blob);
                    }, (err) => {
                        console.error("Failed to stop wb_recording:", err);

                    });
                }
            },

            wb_handleItemClick(menu, submenu = null) {
                const input_msg = this.wb_newMessage;
                this.isLoading = true;
                $.ajax({
                    url: '<?= get_uri('whatsboost/get_ai_response') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        menu: menu,
                        submenu: submenu,
                        input_msg: input_msg,
                    },
                    success: (response) => {
                        if (response.status === false) {
                            appAlert.error(response.message, {
                                duration: 10000
                            });
                        } else {
                            this.wb_newMessage = response.message || input_msg;
                            this.$nextTick(() => {
                                const input = this.$refs.inputField;
                                input.focus();
                            });
                        }
                        this.isLoading = false;
                    },
                })
            },
            ucfirst(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            },

            wb_handleImageAttachmentChange(event) {
                this.wb_imageAttachment = event.target.files[0];
                this.wb_imagePreview = URL.createObjectURL(this.wb_imageAttachment);
                this.showAttachmentOptions = false;
            },
            wb_handleVideoAttachmentChange(event) {
                this.wb_videoAttachment = event.target.files[0];
                this.videoPreview = URL.createObjectURL(this.wb_videoAttachment);
                this.showAttachmentOptions = false;
            },
            wb_handleDocumentAttachmentChange(event) {
                this.wb_documentAttachment = event.target.files[0];
                this.showAttachmentOptions = false;
            },
            wb_removeImageAttachment() {
                this.wb_imageAttachment = null;
                this.imagePreview = '';
            },
            wb_removeVideoAttachment() {
                this.wb_videoAttachment = null;
                this.videoPreview = '';
            },
            wb_removeDocumentAttachment() {
                this.wb_documentAttachment = null;
            },

            wb_formatTime(timestamp) {
                const currentDate = new Date();
                const messageDate = new Date(timestamp);
                const diffInMs = currentDate - messageDate;
                const diffInHours = diffInMs / (1000 * 60 * 60);

                if (diffInHours < 24) {
                    // Less than 24 hours, display time
                    const hour = messageDate.getHours();
                    const minute = messageDate.getMinutes();
                    const period = hour < 12 ? 'AM' : 'PM';
                    const formattedHour = hour % 12 || 12;
                    return `${formattedHour}:${minute < 10 ? '0' + minute : minute} ${period}`;
                } else {
                    // More than 24 hours, display wb_date in dd-mm-yy format
                    const day = messageDate.getDate();
                    const month = messageDate.getMonth() + 1;
                    const year = messageDate.getFullYear() % 100; // Get last two digits of the year
                    return `${day}-${month < 10 ? '0' + month : month}-${year}`;
                }
            },

            wb_alertTime(lastMsgTime) {
                if (lastMsgTime) {
                    // Get the current timestamp in seconds
                    const currentDate = Math.floor(Date.now() / 1000);

                    // Convert lastMsgTime to a timestamp in seconds
                    const messageDate = Math.floor(new Date(lastMsgTime).getTime() / 1000);

                    // Calculate the difference in seconds
                    const diffInSeconds = currentDate - messageDate;

                    // Check if the difference is less than 24 hours (86400 seconds)
                    if (diffInSeconds < 86400) {
                        // Calculate remaining time within 24 hours
                        const remainingSeconds = 86400 - diffInSeconds;

                        const remainingHours = Math.floor(remainingSeconds / 3600);
                        const remainingMinutes = Math.floor((remainingSeconds % 3600) / 60);

                        return `Reply within ${remainingHours} hours and ${remainingMinutes} minutes`;
                    } else {
                        return null;
                    }
                } else {
                    return lastMsgTime;
                }
            },
            wb_stripHTMLTags(str) {
                return str.replace(/<\/?[^>]+(>|$)/g, "");
            },
            wb_truncateText(text, wordLimit) {
                text = this.formatMessage(text);
                const strippedText = this.wb_stripHTMLTags(text);
                const wb_words = strippedText.split(' ');
                if (wb_words.length > wordLimit) {
                    return wb_words.slice(0, wordLimit).join(' ') + '...';
                }
                return text;
            },
            wb_filterInteractions() {
                let filtered = this.interactions;

                if (this.wb_selectedWaNo !== "*") {
                    filtered = filtered.filter(interaction => interaction.wa_no === this.wb_selectedWaNo);
                }
                this.wb_filteredInteractions = filtered;
                this.wb_searchInteractions(); // Call wb_searchInteractions to apply the search text filter
            },

            wb_searchInteractions() {
                if (this.wb_searchText) {
                    this.wb_displayedInteractions = this.wb_filteredInteractions.filter(interaction =>
                        interaction.name.toLowerCase().includes(this.wb_searchText.toLowerCase())
                    );
                } else {
                    this.wb_displayedInteractions = this.wb_filteredInteractions;
                }
            },

            wb_markInteractionAsRead(interactionId) {
                // Immediately update the UI to reflect the interaction as read
                const interaction = this.interactions.find(interaction => interaction.id === interactionId);
                if (interaction) {
                    interaction.read = true; // Assuming there's a 'read' property in your interaction object
                }
                // Send a POST request to mark the interaction as read
                fetch('<?php echo get_uri('whatsboost/mark_interaction_as_read'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            interaction_id: interactionId,
                            csrf_token_name: this.wb_csrfToken
                        }),
                    })
                    .then(wb_response => {
                        if (!wb_response.ok) {
                            throw new Error('Network wb_response was not ok');
                        }
                        return wb_response.json();
                    })
                    .catch(error => {
                        console.error('Error marking interaction as read:', error);
                        // Revert the UI change if there's an error
                        if (interaction) {
                            interaction.read = false;
                        }
                    });
            },

            toggleEmojiPicker() {
                this.wb_showEmojiPicker = !this.wb_showEmojiPicker;
                if (this.wb_showEmojiPicker) {
                    this.initEmojiPicker();
                } else {
                    this.removeEmojiPicker();
                }
            },

            initEmojiPicker() {
                const container = document.getElementById('emoji-picker-container');
                container.innerHTML = '';

                const pickerOptions = {
                    onEmojiSelect: (emoji) => {
                        this.wb_newMessage += emoji.native;
                    }
                };
                const picker = new EmojiMart.Picker(pickerOptions);
                container.appendChild(picker);

                const input = document.getElementById('wb_newMessage');
                const rect = input.getBoundingClientRect();
                const containerRect = container.getBoundingClientRect();

                document.addEventListener('click', this.handleClickOutside);
            },

            removeEmojiPicker() {
                const container = this.$refs.emojiPickerContainer;
                if (container) {
                    container.innerHTML = '';
                }
                document.removeEventListener('click', this.handleClickOutside);
            },

            handleClickOutside(event) {
                const container = this.$refs.emojiPickerContainer;
                if (container && !container.contains(event.target) && !event.target.closest('#emoji_btn')) {
                    this.wb_showEmojiPicker = false;
                    this.removeEmojiPicker();
                }
            },
        },

        watch: {
            wb_displayedInteractions(newInteractions) {
                newInteractions.forEach(interaction => {
                    const previousCount = this.previousCounts[interaction.id] || 0;
                    const currentCount = this.wb_countUnreadMessages(interaction.id);

                    if (currentCount > previousCount) {
                        this.playNotificationSound();
                    }

                    this.$set(this.previousCounts, interaction.id, currentCount);
                });
            }
        },

        created() {
            this.wb_fetchinteractions();
            setInterval(() => {
                this.wb_fetchinteractions();
            }, 5000);
        },
        computed: {
            overdueAlert() {
                const lastMsgTime = this.wb_selectedinteraction.last_msg_time;
                if (lastMsgTime) {
                    const currentDate = new Date();
                    const messageDate = new Date(lastMsgTime);
                    const diffInHours = Math.floor((currentDate - messageDate) / (1000 * 60 * 60));

                    if (diffInHours >= 24) {
                        return `
							<div class="d-flex align-items-center border-0 w-100 px-4 py-3 rounded position-relative mt-4" role="alert" style="background: #fff1b8; color: #b45309">
                            <i class="icon-40 me-2 fw-bold" data-feather="alert-triangle" style="color: #b45309"></i>
                            <span class="d-block d-sm-inline">
                                <span class="fw-semibold">24 hours limit</span>
                                WhatsApp does not allow sending messages 24 hours after they last messaged you. However, you can send them a template message.
                            </span>
                        </div>
                        `;
                    }
                }
                return null;
            },
            wb_selectedInteraction() {
                return this.wb_selectedinteractionIndex !== null ? this.interactions[this.wb_selectedinteractionIndex] : null;
            },
            wb_showSendButton() {
                return this.wb_imageAttachment || this.wb_videoAttachment || this.wb_documentAttachment || this.wb_newMessage.trim();
            },

            wb_uniqueWaNos() {
                // Create a Set to store unique wa_no values
                const wb_uniqueWaNos = new Set();
                // Filter out interactions with duplicate wa_no values
                return this.interactions.filter(interaction => {
                    if (!wb_uniqueWaNos.has(interaction.wa_no)) {
                        wb_uniqueWaNos.add(interaction.wa_no);

                        return true;
                    }
                    return false;
                });
            },
            isButtonEnabled() {
                return this.wb_newMessage.trim().length > 0;
            },
            filteredLanguages() {
                return this.languages.filter(lang => lang.toLowerCase().includes(this.searchQuery.toLowerCase()));
            },
        },
    });
</script>
