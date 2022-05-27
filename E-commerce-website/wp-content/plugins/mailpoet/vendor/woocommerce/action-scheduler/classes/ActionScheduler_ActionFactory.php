<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_ActionFactory {
 public function get_stored_action( $status, $hook, array $args = array(), ActionScheduler_Schedule $schedule = null, $group = '' ) {
 switch ( $status ) {
 case ActionScheduler_Store::STATUS_PENDING :
 $action_class = 'ActionScheduler_Action';
 break;
 case ActionScheduler_Store::STATUS_CANCELED :
 $action_class = 'ActionScheduler_CanceledAction';
 if ( ! is_null( $schedule ) && ! is_a( $schedule, 'ActionScheduler_CanceledSchedule' ) && ! is_a( $schedule, 'ActionScheduler_NullSchedule' ) ) {
 $schedule = new ActionScheduler_CanceledSchedule( $schedule->get_date() );
 }
 break;
 default :
 $action_class = 'ActionScheduler_FinishedAction';
 break;
 }
 $action_class = apply_filters( 'action_scheduler_stored_action_class', $action_class, $status, $hook, $args, $schedule, $group );
 $action = new $action_class( $hook, $args, $schedule, $group );
 return apply_filters( 'action_scheduler_stored_action_instance', $action, $hook, $args, $schedule, $group );
 }
 public function async( $hook, $args = array(), $group = '' ) {
 $schedule = new ActionScheduler_NullSchedule();
 $action = new ActionScheduler_Action( $hook, $args, $schedule, $group );
 return $this->store( $action );
 }
 public function single( $hook, $args = array(), $when = null, $group = '' ) {
 $date = as_get_datetime_object( $when );
 $schedule = new ActionScheduler_SimpleSchedule( $date );
 $action = new ActionScheduler_Action( $hook, $args, $schedule, $group );
 return $this->store( $action );
 }
 public function recurring( $hook, $args = array(), $first = null, $interval = null, $group = '' ) {
 if ( empty($interval) ) {
 return $this->single( $hook, $args, $first, $group );
 }
 $date = as_get_datetime_object( $first );
 $schedule = new ActionScheduler_IntervalSchedule( $date, $interval );
 $action = new ActionScheduler_Action( $hook, $args, $schedule, $group );
 return $this->store( $action );
 }
 public function cron( $hook, $args = array(), $base_timestamp = null, $schedule = null, $group = '' ) {
 if ( empty($schedule) ) {
 return $this->single( $hook, $args, $base_timestamp, $group );
 }
 $date = as_get_datetime_object( $base_timestamp );
 $cron = CronExpression::factory( $schedule );
 $schedule = new ActionScheduler_CronSchedule( $date, $cron );
 $action = new ActionScheduler_Action( $hook, $args, $schedule, $group );
 return $this->store( $action );
 }
 public function repeat( $action ) {
 $schedule = $action->get_schedule();
 $next = $schedule->get_next( as_get_datetime_object() );
 if ( is_null( $next ) || ! $schedule->is_recurring() ) {
 throw new InvalidArgumentException( __( 'Invalid action - must be a recurring action.', 'action-scheduler' ) );
 }
 $schedule_class = get_class( $schedule );
 $new_schedule = new $schedule( $next, $schedule->get_recurrence(), $schedule->get_first_date() );
 $new_action = new ActionScheduler_Action( $action->get_hook(), $action->get_args(), $new_schedule, $action->get_group() );
 return $this->store( $new_action );
 }
 protected function store( ActionScheduler_Action $action ) {
 $store = ActionScheduler_Store::instance();
 return $store->save_action( $action );
 }
}
