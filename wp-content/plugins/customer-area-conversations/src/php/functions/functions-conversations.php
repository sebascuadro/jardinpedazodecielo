<?php
/*  Copyright 2013 MarvinLabs (contact@marvinlabs.com) */

/**
 * Get the number of replies for a given conversation
 *
 * @param int $post_id Defaults to the current post ID from the loop
 *
 * @return array[CUAR_ConversationReply]
 */
function cuar_get_the_conversation_replies($post_id = null)
{
    if ( !$post_id) $post_id = get_the_ID();
    if ( !$post_id) return '';

    $conversation = new CUAR_Conversation($post_id, false);

    return $conversation->get_replies();
}

/**
 * Get the number of participants for a given conversation
 *
 * @param int $post_id Defaults to the current post ID from the loop
 *
 * @return int
 */
function cuar_get_the_conversation_voices($post_id = null)
{
    $replies = cuar_get_the_conversation_replies($post_id);
    $authors = array();
    foreach ($replies as $r)
    {
        $authors[] = $r->post->post_author;
    }
    $authors = array_unique($authors);

    return count($authors);
}

/**
 * @param int $post_id
 * @param int $user_id
 *
 * @return bool|string
 */
function cuar_get_is_conversation_updated($post_id = null, $user_id = null)
{
    if ( !$post_id) $post_id = get_the_ID();
    if ( !$post_id) return '';

    if ( !$user_id) $user_id = get_current_user_id();

    $conversation = new CUAR_Conversation($post_id);

    return $conversation->has_new_replies($user_id);
}

/**
 * @param int $conversation_id
 *
 * @return bool
 */
function cuar_user_can_add_conversation_reply($conversation_id)
{
    /** @var CUAR_ConversationsAddOn $co_addon */
    $co_addon = cuar_addon('conversations');
    return $co_addon->user_can_add_reply($conversation_id);
}

/**
 * @param int $conversation_id
 * @param int $reply_id
 *
 * @return bool
 */
function cuar_user_can_delete_conversation_reply($conversation_id, $reply_id)
{
    /** @var CUAR_ConversationsAddOn $co_addon */
    $co_addon = cuar_addon('conversations');
    return $co_addon->user_can_delete_reply($conversation_id, $reply_id);
}