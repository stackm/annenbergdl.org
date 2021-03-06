/**
 * Courses admin script. Primarily handles deleting users.
 */

var courses = {};
( function( $ ) {
	courses = {
		container: '',
		registered: '',
		waitlist: '',
		settings: coursesOptions || {},

		init: function() {
			courses.container = $( '#anndl_courses_students' );
			courses.registered = courses.container.find( '#courses-registered' );
			courses.waitlist = courses.container.find( '#courses-waitlist' );

			courses.container.on( 'click', '.remove-student', function( e ) {
				if ( confirm( 'Are you sure you want to remove this student from this course (cannot be undone)?' ) ) {
					courses.removeStudent( e.currentTarget );
				} else { // User canceled the confirm modal.
					return;
				}
			});

			courses.container.on( 'change', '.change-certification-status', function( e ) {
				courses.updateStatus( e.currentTarget );
			});

			courses.container.on( 'click', '.add-absence, .subtract-absence', function( e ) {
				courses.updateAbsences( e.currentTarget );
			});
		},

		/**
		 * Moves a user from the waitlist to the registered list.
		 */
		promoteFromWaitlist: function() {
			var student;

			// Find the first student on the waitlist.
			student = courses.waitlist.find( 'tbody tr:first-child' );
			
			// Move student to registered list.
			courses.registered.find( 'tbody' ).append( student );
		},

		/**
		 * Update registered student numbers.
		 */
		updateNumbers: function() {
			var i = 1;
			courses.registered.find( 'tr' ).each( function( i, el ) {
				if ( $( el ).hasClass( 'heading' ) ) {
					return;
				}
				$( el ).find( '.number' ).text( i );
				i = i + 1;
			});
			courses.waitlist.find( 'tr' ).each( function( i, el ) {
				if ( $( el ).hasClass( 'heading' ) ) {
					return;
				}
				$( el ).find( '.number' ).text( i );
				i = i + 1;
			});
		},

		/**
		 * Remove a user from the course, via Ajax.
		 */
		removeStudent: function( el ) {
			var data, row = $( el ).closest( 'tr' );

			// Pull data from the form.
			data = {
				'email': $( el ).data( 'email' ),
				'course': courses.registered.data( 'courseid' ),
				'anndl-students-nonce': $( '#anndl_students_nonce' ).val()
			};

			// Show the form as loading.
			row.css( 'opacity', '.5' );
			// @todo show spinner

			// Send data to the server, and receive a response.
			wp.ajax.send( 'anndl-courses-delete-registration', {
				data: data
			} )
			.done( function( response ) {
				if ( 'deleted' === response ) {
					row.remove();
					courses.promoteFromWaitlist();
					courses.updateNumbers();
				} else {
					alert( 'Error processing request. Please try again. Error code:' + response );
					row.css( 'opacity', 1 );
					// @todo hide spinner
				}
			} )
			.fail( function( response ) {
				alert( 'Error processing request. Please try again. Error code:' + response );
				row.css( 'opacity', 1 );
				// @todo hide spinner
			} );
		},

		/**
		 * Update a student's certification status, via Ajax.
		 */
		updateStatus: function( el ) {
			var data;

			// Pull data from the form.
			data = {
				'email': $( el ).data( 'email' ),
				'status': $( el ).val(),
				'course': courses.registered.data( 'courseid' ),
				'anndl-students-nonce': $( '#anndl_students_nonce' ).val()
			};

			// Show the form as loading.
			$( el ).css( 'opacity', '.5' );

			// Send data to the server, and receive a response.
			wp.ajax.send( 'anndl-courses-change-certification-status', {
				data: data
			} )
			.done( function( response ) {
				if ( 'updated' === response ) {
					$( el ).css( 'opacity', '1' );
				} else {
					alert( 'Error processing request. Please try again. Error code: ' + response );
					$( el ).css( 'opacity', 1 );
				}
			} )
			.fail( function( response ) {
				alert( 'Error processing request. Please try again. Error code:' + response );
				$( el ).css( 'opacity', 1 );
			} );
		},

		/**
		 * Update a student's absences, via Ajax.
		 */
		updateAbsences: function( el ) {
			var data, row = $( el ).closest( 'tr' ), change, absences;

			if ( $( el ).hasClass( 'add-absence' ) ) {
				change = 1;
			} else {
				change = -1;
			}
			absences = change + parseInt( row.find( '.num-absences' ).text() );
			if ( absences < 0 ) {
				return;
			}

			// Pull data from the form.
			data = {
				'email': row.data( 'email' ),
				'absences': absences,
				'course': courses.registered.data( 'courseid' ),
				'anndl-students-nonce': $( '#anndl_students_nonce' ).val()
			};

			// Show the form as loading.
			$( el ).css( 'opacity', '.5' );

			// Send data to the server, and receive a response.
			wp.ajax.send( 'anndl-courses-update-absences', {
				data: data
			} )
			.done( function( response ) {
				if ( 'updated' === response ) {
					$( el ).css( 'opacity', '1' );
					row.find( '.num-absences' ).text( absences );
				} else {
					alert( 'Error processing request. Please try again. Error code: ' + response );
					$( el ).css( 'opacity', 1 );
				}
			} )
			.fail( function( response ) {
				alert( 'Error processing request. Please try again. Error code:' + response );
				$( el ).css( 'opacity', 1 );
			} );
		}
	}

	$( document ).ready( function() { courses.init(); } );

} )( jQuery );