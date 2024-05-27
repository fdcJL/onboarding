<?php

App::uses('AppHelper', 'View/Helper');

class TimeHelper extends AppHelper {

    /**
     * Format timestamp as "time ago"
     *
     * @param int $timestamp Unix timestamp
     * @return string Formatted time ago string
     */
    public function timeAgo($timestamp) {
        $current_time = time();
        $time_diff = $current_time - $timestamp;
        $seconds = $time_diff;
        $minutes = round($seconds / 60);
        $hours = round($seconds / 3600);
        $days = round($seconds / 86400);
        $weeks = round($seconds / 604800);
        $months = round($seconds / 2629440);
        $years = round($seconds / 31553280);

        if ($seconds <= 60) {
            return 'just now';
        } elseif ($minutes <= 60) {
            if ($minutes == 1) {
                return '1 minute ago';
            } else {
                return "$minutes minutes ago";
            }
        } elseif ($hours <= 24) {
            if ($hours == 1) {
                return '1 hour ago';
            } else {
                return "$hours hours ago";
            }
        } elseif ($days <= 7) {
            if ($days == 1) {
                return 'yesterday';
            } else {
                return "$days days ago";
            }
        } elseif ($weeks <= 4.3) {
            if ($weeks == 1) {
                return '1 week ago';
            } else {
                return "$weeks weeks ago";
            }
        } elseif ($months <= 12) {
            if ($months == 1) {
                return '1 month ago';
            } else {
                return "$months months ago";
            }
        } else {
            if ($years == 1) {
                return '1 year ago';
            } else {
                return "$years years ago";
            }
        }
    }
}