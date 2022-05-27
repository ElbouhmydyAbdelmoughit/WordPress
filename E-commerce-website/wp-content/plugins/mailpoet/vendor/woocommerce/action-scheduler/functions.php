<?php
if (!defined('ABSPATH')) exit;
function as_enqueue_async_action( $hook, $args = array(), $group = '' ) {
 if ( ! ActionScheduler::is_initialized( __FUNCTION__ ) ) {
 return 0;
 }
 return ActionScheduler::factory()->async( $hook, $args, $group );
}
function as_schedule_single_action( $timestamp, $hook, $args = array(), $group = '' ) {
 if ( ! ActionScheduler::is_initialized( __FUNCTION__ ) ) {
 return 0;
 }
 return ActionScheduler::factory()->single( $hook, $args, $timestamp, $group );
}
function as_schedule_recurring_action( $timestamp, $interval_in_seconds, $hook, $args = array(), $group = '' ) {
 if ( ! ActionScheduler::is_initialized( __FUNCTION__ ) ) {
 return 0;
 }
 return ActionScheduler::factory()->recurring( $hook, $args, $timestamp, $interval_in_seconds, $group );
}
function as_schedule_cron_action( $timestamp, $schedule, $hook, $args = array(), $group = '' ) {
 if ( ! ActionScheduler::is_initialized( __FUNCTION__ ) ) {
 return 0;
 }
 return ActionScheduler::factory()->cron( $hook, $args, $timestamp, $schedule, $group );
}
function as_unschedule_action( $hook, $args = array(), $group = '' ) {
 if ( ! ActionScheduler::is_initialized( __FUNCTION__ ) ) {
 return 0;
 }
 $params = array(
 'hook' => $hook,
 'status' => ActionScheduler_Store::STATUS_PENDING,
 'orderby' => 'date',
 'order' => 'ASC',
 'group' => $group,
 );
 if ( is_array( $args ) ) {
 $params['args'] = $args;
 }
 $action_id = ActionScheduler::store()->query_action( $params );
 if ( $action_id ) {
 ActionScheduler::store()->cancel_action( $action_id );
 }
 return $action_id;
}
function as_unschedule_all_actions( $hook, $args = array(), $group = '' ) {
 if ( ! ActionScheduler::is_initialized( __FUNCTION__ ) ) {
 return;
 }
 if ( empty( $args ) ) {
 if ( ! empty( $hook ) && empty( $group ) ) {
 ActionScheduler_Store::instance()->cancel_actions_by_hook( $hook );
 return;
 }
 if ( ! empty( $group ) && empty( $hook ) ) {
 ActionScheduler_Store::instance()->cancel_actions_by_group( $group );
 return;
 }
 }
 do {
 $unscheduled_action = as_unschedule_action( $hook, $args, $group );
 } while ( ! empty( $unscheduled_action ) );
}
function as_next_scheduled_action( $hook, $args = null, $group = '' ) {
 if ( ! ActionScheduler::is_initialized( __FUNCTION__ ) ) {
 return false;
 }
 $params = array(
 'hook' => $hook,
 'orderby' => 'date',
 'order' => 'ASC',
 'group' => $group,
 );
 if ( is_array( $args ) ) {
 $params['args'] = $args;
 }
 $params['status'] = ActionScheduler_Store::STATUS_RUNNING;
 $action_id = ActionScheduler::store()->query_action( $params );
 if ( $action_id ) {
 return true;
 }
 $params['status'] = ActionScheduler_Store::STATUS_PENDING;
 $action_id = ActionScheduler::store()->query_action( $params );
 if ( null === $action_id ) {
 return false;
 }
 $action = ActionScheduler::store()->fetch_action( $action_id );
 $scheduled_date = $action->get_schedule()->get_date();
 if ( $scheduled_date ) {
 return (int) $scheduled_date->format( 'U' );
 } elseif ( null === $scheduled_date ) { // pending async action with NullSchedule
 return true;
 }
 return false;
}
function as_has_scheduled_action( $hook, $args = null, $group = '' ) {
 if ( ! ActionScheduler::is_initialized( __FUNCTION__ ) ) {
 return false;
 }
 $query_args = array(
 'hook' => $hook,
 'status' => array( ActionScheduler_Store::STATUS_RUNNING, ActionScheduler_Store::STATUS_PENDING ),
 'group' => $group,
 'orderby' => 'none',
 );
 if ( null !== $args ) {
 $query_args['args'] = $args;
 }
 $action_id = ActionScheduler::store()->query_action( $query_args );
 return $action_id !== null;
}
function as_get_scheduled_actions( $args = array(), $return_format = OBJECT ) {
 if ( ! ActionScheduler::is_initialized( __FUNCTION__ ) ) {
 return array();
 }
 $store = ActionScheduler::store();
 foreach ( array('date', 'modified') as $key ) {
 if ( isset($args[$key]) ) {
 $args[$key] = as_get_datetime_object($args[$key]);
 }
 }
 $ids = $store->query_actions( $args );
 if ( $return_format == 'ids' || $return_format == 'int' ) {
 return $ids;
 }
 $actions = array();
 foreach ( $ids as $action_id ) {
 $actions[$action_id] = $store->fetch_action( $action_id );
 }
 if ( $return_format == ARRAY_A ) {
 foreach ( $actions as $action_id => $action_object ) {
 $actions[$action_id] = get_object_vars($action_object);
 }
 }
 return $actions;
}
function as_get_datetime_object( $date_string = null, $timezone = 'UTC' ) {
 if ( is_object( $date_string ) && $date_string instanceof DateTime ) {
 $date = new ActionScheduler_DateTime( $date_string->format( 'Y-m-d H:i:s' ), new DateTimeZone( $timezone ) );
 } elseif ( is_numeric( $date_string ) ) {
 $date = new ActionScheduler_DateTime( '@' . $date_string, new DateTimeZone( $timezone ) );
 } else {
 $date = new ActionScheduler_DateTime( $date_string, new DateTimeZone( $timezone ) );
 }
 return $date;
}
