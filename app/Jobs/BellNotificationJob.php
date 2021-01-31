<?php

namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Helpers\Helper;

use Log; 

use App\Setting;

use App\User;

use App\BellNotification;

use App\BellNotificationTemplate;

use App\Jobs\Job;

class BellNotificationJob extends Job implements ShouldQueue
{    
    use InteractsWithQueue, SerializesModels;

    protected $data, $status, $content, $receiver, $booking_id, $from, $to, $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $status, $content, $receiver, $booking_id, $from, $to, $type = '')
    {
        $this->data = $data;
        $this->status = $status;
        $this->content = $content;
        $this->receiver = $receiver;
        $this->booking_id = $booking_id;
        $this->from = $from;
        $this->to = $to;
        $this->type = $type;
        Log::info('const call');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            Log::info('job call');

            $datas = $this->data;
            
            $bell_notification_details = new BellNotification;

            $bell_notification_details->from_id = $this->from;

            $bell_notification_details->to_id = $this->to;

            $bell_notification_details->notification_type = $this->status;

            $bell_notification_details->receiver = $this->receiver;

            $bell_notification_details->message = $this->content;

            $bell_notification_details->booking_id = $this->booking_id;

            $bell_notification_details->host_id = $this->type ? $datas->id : $datas->host_id;
            
            $bell_notification_details->status = BELL_NOTIFICATION_STATUS_UNREAD;

            $bell_notification_details->save();
            
        } catch(Exception $e) {

            Log::info("BellNotificationJob - ERROR".print_r($e->getMessage(), true));
        }
        
    }
}
