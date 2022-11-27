<?php

namespace App;

/**
 * Flash notification messages: messages for one-time display using the session
 * for storage between requests.
 */
class Flash
{

    /**
     * Success message type
     * @var string
     */
    const SUCCESS = 'success';

    /**
     * Information message type
     * @var string
     */
    const INFO = 'info';

    /**
     * Warning message type
     * @var string
     */
    const WARNING = 'warning';

    /**
     * Add a message
     *
     * @param string $message  The message content
     * @param string $type  The optional message type, defaults to SUCCESS
     *
     * @return void
     */
    public static function addMessage($key, $message, $type = 'success')
    {
        // Create array in the session if it doesn't already exist
        if (! isset($_SESSION['flashNotifications'])) {
            $_SESSION['flashNotifications'] = [];
        }

        // Append the message to the array
        $_SESSION['flashNotifications'][$key] = [
            'body' => $message,
            'type' => $type
        ];
    }

    /**
     * Get all the messages
     *
     * @return mixed  An array with all the messages or null if none set
     */
    public static function getMessages()
    {
        if (isset($_SESSION['flashNotifications'])) {
            
            $messages = $_SESSION['flashNotifications'];
            unset($_SESSION['flashNotifications']);

            return $messages;
        }
    }
}
