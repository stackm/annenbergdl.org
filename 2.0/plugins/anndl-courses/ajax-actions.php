<?php
/**
 * Ajax actions for the Courses plugin.
 */

/**
 * Registeres a user for a course.
 *
 * Anyone can register, so nonces are not used/checked here.
 */
function anndl_courses_register_student() {
	$course_id = absint( $_POST['course'] );
	
	$course = get_post( $course_id );
	if ( ! $course || is_wp_error( $course ) ) {
		wp_send_json_error( 'invalid_course' );
	}
	
	$students = get_post_meta( $course_id, '_students', true );
	if ( '' === $students ) {
		$students = array();
	} else {
		$students = $students;
	}
	
	// Validate and sanitize student's email and USC ID.
	if ( '@usc.edu' !== substr( $_POST['email'], -8 ) ) {
		wp_send_json_error( 'invalid_email' );
	} else {
		$email = sanitize_text_field( substr( $_POST['email'], 0, -8 ) );
	}
	if ( 10 !== strlen( $_POST['id'] ) ) {
		wp_send_json_error( 'invalid_id' );
	} else {
		$id = absint( $_POST['id'] );
	}
	$name = sanitize_text_field( $_POST['name'] );
	$major = sanitize_text_field( $_POST['major'] );

	// Make sure the student hasn't already registered for this course.
	if ( array_key_exists( $email, $students ) ) {
		wp_send_json_errror( 'already_registered' );
	}
	
	// Make sure the student hasn't registered for any other courses this semester.
	// @todo get all courses in this semester and check their students
	
	$student = array(
		'name' => $name,
		'id' => $id,
		'major' => $major,
		'certified' => 'no',
	);
	
	$students[$email] = $student;
	$result = update_post_meta( $course->ID, '_students', $students ); // Automatically re-serializes the array. $course_id does not work here for some reason.
	if ( $result ) {
		$capacity = get_post_meta( $course_id, 'capacity', true );
		if ( ! $capacity ) {
			$capacity = 32;
		}
		if ( count( $students ) > $capacity ) {
			wp_send_json_success( 'waitlist' );
		} else {
			wp_send_json_success( 'registered' );
		}
	} else {
		wp_send_json_error( 'unknown_error' );
	}

}
add_action( 'wp_ajax_anndl-course-registration', 'anndl_courses_register_student' );
add_action( 'wp_ajax_nopriv_anndl-course-registration', 'anndl_courses_register_student' );


/**
 * Delete a student's registration for a course.
 *
 * Only authenticated users can delete registrations.
 */
function anndl_courses_delete_registration() {
	check_ajax_referer( 'anndl_students_nonce', 'anndl-students-nonce' );

	$course_id = absint( $_POST['course'] );
	$course = get_post( $course_id );
	if ( ! $course || is_wp_error( $course ) ) {
		wp_send_json_error( 'invalid_course' );
	}

	$student = sanitize_text_field( $_POST['email'] );
	$students = get_post_meta( $course_id, '_students', true );
	if ( '' === $students || ! array_key_exists( $student, $students ) ) {
		wp_send_json_error( 'invalid_student' );
	} else {
		unset( $students[$student] );
	}
	$result = update_post_meta( $course->ID, '_students', $students ); // Automatically re-serializes the array. $course_id does not work here for some reason.
	if ( $result ) {
		wp_send_json_success( 'deleted' );
	} else {
		wp_send_json_error( 'could_not_update_student_data' );
	}
}
add_action( 'wp_ajax_anndl-courses-delete-registration', 'anndl_courses_delete_registration' );