<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_WPCLI_Scheduler_command extends WP_CLI_Command {
 public function run( $args, $assoc_args ) {
 // Handle passed arguments.
 $batch = absint( \WP_CLI\Utils\get_flag_value( $assoc_args, 'batch-size', 100 ) );
 $batches = absint( \WP_CLI\Utils\get_flag_value( $assoc_args, 'batches', 0 ) );
 $clean = absint( \WP_CLI\Utils\get_flag_value( $assoc_args, 'cleanup-batch-size', $batch ) );
 $hooks = explode( ',', WP_CLI\Utils\get_flag_value( $assoc_args, 'hooks', '' ) );
 $hooks = array_filter( array_map( 'trim', $hooks ) );
 $group = \WP_CLI\Utils\get_flag_value( $assoc_args, 'group', '' );
 $free_on = \WP_CLI\Utils\get_flag_value( $assoc_args, 'free-memory-on', 50 );
 $sleep = \WP_CLI\Utils\get_flag_value( $assoc_args, 'pause', 0 );
 $force = \WP_CLI\Utils\get_flag_value( $assoc_args, 'force', false );
 ActionScheduler_DataController::set_free_ticks( $free_on );
 ActionScheduler_DataController::set_sleep_time( $sleep );
 $batches_completed = 0;
 $actions_completed = 0;
 $unlimited = $batches === 0;
 try {
 // Custom queue cleaner instance.
 $cleaner = new ActionScheduler_QueueCleaner( null, $clean );
 // Get the queue runner instance
 $runner = new ActionScheduler_WPCLI_QueueRunner( null, null, $cleaner );
 // Determine how many tasks will be run in the first batch.
 $total = $runner->setup( $batch, $hooks, $group, $force );
 // Run actions for as long as possible.
 while ( $total > 0 ) {
 $this->print_total_actions( $total );
 $actions_completed += $runner->run();
 $batches_completed++;
 // Maybe set up tasks for the next batch.
 $total = ( $unlimited || $batches_completed < $batches ) ? $runner->setup( $batch, $hooks, $group, $force ) : 0;
 }
 } catch ( Exception $e ) {
 $this->print_error( $e );
 }
 $this->print_total_batches( $batches_completed );
 $this->print_success( $actions_completed );
 }
 protected function print_total_actions( $total ) {
 WP_CLI::log(
 sprintf(
 _n( 'Found %d scheduled task', 'Found %d scheduled tasks', $total, 'action-scheduler' ),
 number_format_i18n( $total )
 )
 );
 }
 protected function print_total_batches( $batches_completed ) {
 WP_CLI::log(
 sprintf(
 _n( '%d batch executed.', '%d batches executed.', $batches_completed, 'action-scheduler' ),
 number_format_i18n( $batches_completed )
 )
 );
 }
 protected function print_error( Exception $e ) {
 WP_CLI::error(
 sprintf(
 __( 'There was an error running the action scheduler: %s', 'action-scheduler' ),
 $e->getMessage()
 )
 );
 }
 protected function print_success( $actions_completed ) {
 WP_CLI::success(
 sprintf(
 _n( '%d scheduled task completed.', '%d scheduled tasks completed.', $actions_completed, 'action-scheduler' ),
 number_format_i18n( $actions_completed )
 )
 );
 }
}
