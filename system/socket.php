<?php
/**
 * FuseChat Backend Script (Enhanced)
 *
 * @package FuseChat
 * @author www.nemra-1.com
 * @copyright 2025
 * @terms Unauthorized use prohibited
 */

function notifyRedis($event, $data) {
    // Connect to Redis
    $redis = new Redis();
    try {
        $redis->connect('127.0.0.1', 6379); // Default Redis host and port
        // Publish the event and data to the 'chat' channel
        $redis->publish('chat', json_encode([
            'event' => $event,
            'data' => $data
        ]));
    } catch (Exception $e) {
        error_log("Redis error: " . $e->getMessage());
    } finally {
        $redis->close(); // Close the connection
    }
}

?>