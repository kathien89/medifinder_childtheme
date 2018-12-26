<?php

if (!defined('FW')) {
    die('Forbidden');
}


$options = array(
    'notification_settings' => array(
        'type' => 'tab',
        'title' => esc_html__('Email Settings', 'docdirect'),
        'options' => array(
			'email_from_name' => array(
				'type'  => 'text',
				'value' => 'DocDirect',
				'label' => esc_html__('Email From Name', 'docdirect'),
				'desc'  => esc_html__('Add FROM NAME when email sent. Like: DocDirect', 'docdirect'),
			),
			'email_from_id' => array(
				'type'  => 'text',
				'value' => 'info@no-reply.com',
				'label' => esc_html__('FROM : Email ID', 'docdirect'),
				'desc'  => esc_html__('Add FROM EMAIL when email sent. Like: info@no-reply.com', 'docdirect'),
			),
			'notification_settings' => array(
				'type' => 'tab',
				'title' => esc_html__('General Templates', 'docdirect'),
				'options' => array(
					'register_user' => array(
						'title' => esc_html__('Email Content - Registration', 'docdirect'),
						'type' => 'tab',
						'options' => array(
							'register_subject' => array(
								'type' => 'text',
								'value' => 'Thank you for registering!',
								'label' => esc_html__('Subject', 'docdirect'),
								'desc' => esc_html__('Please add Subject for email', 'docdirect'),
							),
							'info' => array(
								'type'  => 'html',
								'value' => '',
								'attr'  => array(),
								'label' => esc_html__('Email Settings variables', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '%name% — To display the person\'s name. <br/>
								%email% — To display the person\'s email address.<br/>
								%username% — To display the username for login.<br/>
								%password% — To display the password for login.<br/>
								%logo% — To display site logo.<br/>',
							),
							'register_content' => array(
								'type'  => 'wp-editor',
								'value' => 'Hey %name%!<br/>

											Thanks for registering at DocDirect. You can now login to manage your account using the following credentials:
											<br/>
											Username: %username%<br/>
											Password: %password%<br/>

											Sincerely,<br/>
											DocDirect Team<br/>
											%logo%
											',
								'label' => esc_html__('Email Contents', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),

								/**
								 * Also available
								 * https://github.com/WordPress/WordPress/blob/4.4.2/wp-includes/class-wp-editor.php#L80-L94
								 */
							)
						)
					),
					'package_payment' => array(
						'title' => esc_html__('Payments (Invoice Detail)', 'docdirect'),
						'type' => 'tab',
						'options' => array(
							'invoice_subject' => array(
								'type' => 'text',
								'value' => 'Thank you for purchasing package!',
								'label' => esc_html__('Subject', 'docdirect'),
								'desc' => esc_html__('Please add Subject for email', 'docdirect'),
							),
							'info' => array(
								'type'  => 'html',
								'value' => '',
								'attr'  => array(),
								'label' => esc_html__('Email Settings variables', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '%name% — To display the person\'s name. <br/>
								%email% — To display the person\'s email address.<br/>
								%invoice% — To display the invoice id for payment.<br/>
								%package_name% — To display the package name.<br/>
								%amount% — To display the payment amount.<br/>
								%status% — To display the payment status.<br/>
								%method% — To display payment mehtod.<br/>
								%date% — To display purchase date.<br/>
								%expiry% — To display package expiry date.<br/>
								%address% — To display payer address.<br/>
								%logo% — To display site logo.<br/>',

							),
							'payment_content' => array(
								'type'  => 'wp-editor',
								'value' => 'Hey %name%!<br/>

											Thanks for purchasing the package. Your payment has been received and your invoice detail is given below:
											<br/>
											Invoice ID: %invoice%<br/>
											Package Name: %package_name%<br/>
											Payment Amount: %amount%<br/>
											Payment status: %status%<br/>
											Payment Method: %method%<br/>
											Purchase Date: %date%<br/>
											Expiry Date: %expiry%<br/>
											Address: %address%<br/>

											Sincerely,<br/>
											DocDirect Team<br/>
											%logo%
											',
								'attr'  => array(),
								'label' => esc_html__('Email Contents', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'size' => 'large', // small, large
								'editor_height' => 400,

								/**
								 * Also available
								 * https://github.com/WordPress/WordPress/blob/4.4.2/wp-includes/class-wp-editor.php#L80-L94
								 */
							)
						)
					),
					'rating' => array(
						'title' => esc_html__('Rating ( Received )', 'docdirect'),
						'type' => 'tab',
						'options' => array(
							'rating_subject' => array(
								'type' => 'text',
								'value' => 'New rating received!',
								'label' => esc_html__('Subject', 'docdirect'),
								'desc' => esc_html__('Please add Subject for email', 'docdirect'),
							),
							'info' => array(
								'type'  => 'html',
								'value' => '',
								'attr'  => array(),
								'label' => esc_html__('Email Settings variables', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '%name% — To display the person\'s name. <br/>
								%rating_from% — To display the person name who rate user.<br/>
								%reason% — To display the rating subject.<br/>
								%link% — To display the rating page link.<br/>
								%rating% — To display the rating.<br/>
								%logo% — To display site logo.<br/>
								%detail_rating% — To display detail_rating.<br/>',

							),
							'rating_content' => array(
								'type'  => 'wp-editor',
								'value' => 'Hey %name%!<br/>

											A new rating has been received, Detail for rating is given below:
											<br/>
											Rating: %rating%<br/>
											Rating From: %rating_from%<br/>
											Reason: %reason%<br/>
											Comment: <br/>
											---------------------------------------<br/>
											You can view this at %link%

											Sincerely,<br/>
											DocDirect Team<br/>
											%logo%
											',
								'attr'  => array(),
								'label' => esc_html__('Email Contents', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'size' => 'large', // small, large
								'editor_height' => 400,

								/**
								 * Also available
								 * https://github.com/WordPress/WordPress/blob/4.4.2/wp-includes/class-wp-editor.php#L80-L94
								 */
							)
						)
					),
					'account_verification_email' => array(
                        'title' => esc_html__('Account Verification', 'docdirect'),
                        'type' => 'tab',
                        'options' => array(
                            'ave_subject' => array(
                                'type' => 'text',
                                'value' => 'Account Verification',
                                'label' => esc_html__('Subject', 'docdirect'),
                                'desc' => esc_html__('Please add subject for account verification.', 'docdirect'),
                            ),
                            'ave_info' => array(
                                'type' => 'html',
                                'value' => '',
                                'attr' => array(),
                                'label' => esc_html__('Email settings', 'docdirect'),
                                'desc' => esc_html__('', 'docdirect'),
                                'help' => esc_html__('', 'docdirect'),
                                'html' => '%name% — To display the person\'s name. <br/>
								%link% — Verify account link.<br/>
								%logo% — To display site logo.<br/>',
                            ),
                            'ave_content' => array(
                                'type' => 'wp-editor',
                                'value' => 'Hi %name%!<br/>

											<p><strong>Verify Your Account</strong></p>
											<p>You account has created with given below email address:</p>
											<p>Email Address: %account_email%</p>
											<p>If this was a mistake, just ignore this email and nothing will happen.</p>
											<p>To verifiy your account, click below link:</p>
											<p><a style="color: #fff; padding: 0 50px; margin: 0 0 15px; font-size: 20px; font-weight: 600; line-height: 60px; border-radius: 8px; background: #5dc560; vertical-align: top; display: inline-block; font-family: "Work Sans", Arial, Helvetica, sans-serif; text-decoration: none;" href="%link%">Verify</a></p><br />
											Sincerely,<br/>
											%logo%
											',
                                'attr' => array(),
                                'label' => esc_html__('Account Verification?', 'docdirect'),
                                'desc' => esc_html__('', 'docdirect'),
                                'help' => esc_html__('', 'docdirect'),
                                'size' => 'large', // small, large
                                'editor_height' => 400,
                            )
                        ),
                    ),
					'invitation' => array(
						'title' => esc_html__('Invitation', 'docdirect'),
						'type' => 'tab',
						'options' => array(
							'invitation_subject' => array(
								'type' => 'text',
								'value' => 'You have invitation for signup!',
								'label' => esc_html__('Subject', 'docdirect'),
								'desc' => esc_html__('Please add Subject for email', 'docdirect'),
							),
							'invitation_info' => array(
								'type'  => 'html',
								'value' => '',
								'attr'  => array(),
								'label' => esc_html__('Email Settings variables', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '%username% — To display the username who send invitation. <br/>
								%link% — To display link for signup.<br/>
								%message% — To display user message.<br/>
								%logo% — To display site logo.<br/>',

							),
							'invitation_content' => array(
								'type'  => 'wp-editor',
								'value' => 'Hi,<br/>

											%username% has invited you to signup at %link%. You have invitation message given below
											<br/>
											%message%
											<br/>
											Sincerely,<br/>
											DocDirect Team<br/>
											%logo%
											',
								'attr'  => array(),
								'label' => esc_html__('Email Contents', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'size' => 'large', // small, large
								'editor_height' => 400,
							)
						)
					),
					'contact' => array(
						'title' => esc_html__('Contact Form', 'docdirect'),
						'type' => 'tab',
						'options' => array(
							'contact_email'     => array(
								'type'  => 'html',
								'value' => '',
								'label' => esc_html__('', 'docdirect'),
								'desc'  => esc_html__('This template will be use for professional users and site contact form to send an email from vistors.', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '',
							),
							'contact_subject' => array(
								'type' => 'text',
								'value' => 'Contact Form Received',
								'label' => esc_html__('Subject', 'docdirect'),
								'desc' => esc_html__('Please add Subject for email', 'docdirect'),
							),
							'contact_info' => array(
								'type'  => 'html',
								'value' => '',
								'attr'  => array(),
								'label' => esc_html__('Email Settings variables', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '%subject% — Contact subject <br/>
								%name% — To display username who contact the professional.<br/>
								%email% — To display email who contact the professional.<br/>
								%phone% — To display phone number who contact the professional.<br/>
								%message% — To display user message.<br/>
								%doctor_name% — To display doctor name.<br/>
								%logo% — To display site logo.<br/>',

							),
							'contact_content' => array(
								'type'  => 'wp-editor',
								'value' => 'Hello,<br/>

											A person has contact you, description of message is given below.<br/><br/>
											Subject : %subject%<br/>
											Name : %name%<br/>
											Email : %email%<br/>
											Phone Number : %phone%<br/>
											Message : %message%<br/><br/><br/>

											Sincerely,<br/>
											%logo%
											',
								'attr'  => array(),
								'label' => esc_html__('Email Contents', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'size' => 'large', // small, large
								'editor_height' => 400,
							)
						)
					),
					'lp_email' => array(
						'title' => esc_html__('Lost Password', 'docdirect'),
						'type' => 'tab',
						'options' => array(
							'lp_subject' => array(
								'type' => 'text',
								'value' => 'Forgot Password',
								'label' => esc_html__('Subject', 'docdirect'),
								'desc' => esc_html__('Please add subject for lost password.', 'docdirect'),
							),
							'lp_info' => array(
								'type' => 'html',
								'value' => '',
								'attr' => array(),
								'label' => esc_html__('Email settings', 'docdirect'),
								'desc' => esc_html__('', 'docdirect'),
								'help' => esc_html__('', 'docdirect'),
								'html' => '%username% — To display the person\'s name. <br/>
								%link% — To display the lost password link.<br/>
								%logo% — To display site logo.<br/>',
							),
							'lp_content' => array(
								'type' => 'wp-editor',
								'value' => 'Hey %name%!<br/>

											<p><strong>Lost Password reset</strong></p>
											<p>Someone requested that the password be reset for the following account:</p>
											<p>Email Address: %account_email%</p>
											<p>If this was a mistake, just ignore this email and nothing will happen.</p>
											<p>To reset your password, click reset link below:</p>
											<p><a href="%link%">Reset</a></p>
											Sincerely,<br/>
											DocDirect Team<br/>
											%logo%
											',
								'attr' => array(),
								'label' => esc_html__('Lost Password?', 'docdirect'),
								'desc' => esc_html__('', 'docdirect'),
								'help' => esc_html__('', 'docdirect'),
								'size' => 'large', // small, large
								'editor_height' => 400,
							)
						),
					),
				)
			),
			'admin_settings' => array(
				'type' => 'tab',
				'title' => esc_html__('Admin Templates', 'docdirect'),
				'options' => array(
					'admin_email' => array(
						'title' => esc_html__('Admin Email Content - Registration', 'docdirect'),
						'type' => 'tab',
						'options' => array(
							'admin_email_section' => array(
								'type' => 'html',
								'html' => esc_html__('Admin Email', 'docdirect'),
								'label' => esc_html__('', 'docdirect'),
								'desc' => esc_html__('This email will be sent to admin when new user register on your site.', 'docdirect'),
								'help' => esc_html__('', 'docdirect'),
								'images_only' => true,
							),
							'admin_register_subject' => array(
								'type' => 'text',
								'value' => 'New Registration!',
								'label' => esc_html__('Subject', 'docdirect'),
								'desc' => esc_html__('Add email subject.', 'docdirect'),
							),
							'admin_email' => array(
								'type' => 'text',
								'value' => 'info@yourdomain.com',
								'label' => esc_html__('Admin email address', 'docdirect'),
								'desc' => esc_html__('Please add admin email address, leave it empty to get email address from WordPress Settings.', 'docdirect'),
							),
							'admin_info' => array(
								'type'  => 'html',
								'value' => '',
								'attr'  => array(),
								'label' => esc_html__('Email Settings variables', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '%username% — To display new registered  username. <br/>
								%link% — To display the new registered user profile page at admin site.<br/>
								%email% — To display the username for login.<br/>
								%logo% — To display site logo.<br/>',
							),
							'admin_register_content' => array(
								'type'  => 'wp-editor',
								'value' => 'Hey<br/>

											A new user "%username%" with email address "%email%" has registered on your website. Please login to check user detail.
											<br/>
											You can check user detail at: %link%<br/><br/><br/>

											Sincerely,<br/>
											%logo%
											',
								'attr'  => array(),
								'label' => esc_html__('Email Contents', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'size' => 'large', // small, large
								'editor_height' => 400,

								/**
								 * Also available
								 * https://github.com/WordPress/WordPress/blob/4.4.2/wp-includes/class-wp-editor.php#L80-L94
								 */
							)
						)
					),
					'claim' => array(
						'title' => esc_html__('Claim/Remprt Email', 'docdirect'),
						'type' => 'tab',
						'options' => array(
							'claim_admin_email' => array(
								'type' => 'text',
								'value' => 'info@yourdomain.com',
								'label' => esc_html__('Admin email address to send claim/report email', 'docdirect'),
								'desc' => esc_html__('Please add admin email address, leave it empty to get email address from WordPress Settings.', 'docdirect'),
							),
							'claim_subject' => array(
								'type' => 'text',
								'value' => 'A user has claimed!',
								'label' => esc_html__('Subject', 'docdirect'),
								'desc' => esc_html__('Please add subject for email', 'docdirect'),
							),
							'claim_info' => array(
								'type'  => 'html',
								'value' => '',
								'attr'  => array(),
								'label' => esc_html__('Email Settings variables', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '%claimed_user% — To display the username who has claimed. <br/>
								%claimed_by% — To display the username who has claimed. <br/>
								%message% — To display message of visitor user.<br/>
								%logo% — To display site logo(Optional)<br/>',

							),
							'claim_content' => array(
								'type'  => 'wp-editor',
								'value' => 'Hi,<br/>
											%claimed_user% has claimed by %claimed_by%
											<br/><br/>
											Message is given below.
											<br/>
												%message%
											<br/><br/>
											Sincerely,<br/>
											DocDirect Team<br/>
											%logo%
											',
								'attr'  => array(),
								'label' => esc_html__('Email Contents', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'size' => 'large', // small, large
								'editor_height' => 400,
							)
						)
					),
					'end_trial_email' => array(
						'title' => esc_html__('End Free Trail Email', 'docdirect'),
						'type' => 'tab',
						'options' => array(
							'end_trial_admin_email' => array(
								'type' => 'text',
								'value' => 'info@yourdomain.com',
								'label' => esc_html__('Admin email address to send email', 'docdirect'),
								'desc' => esc_html__('Please add admin email address, leave it empty to get email address from WordPress Settings.', 'docdirect'),
							),
							'end_trial_subject' => array(
								'type' => 'text',
								'value' => 'End Free Trail!',
								'label' => esc_html__('Subject', 'docdirect'),
								'desc' => esc_html__('Please add subject for email', 'docdirect'),
							),
							'end_trial_info' => array(
								'type'  => 'html',
								'value' => '',
								'attr'  => array(),
								'label' => esc_html__('Email Settings variables', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '%username% — To display the username. <br/>
								%logo% — To display site logo(Optional)<br/>
								%linkpackage% — linkpackage<br/>',

							),
							'end_trial_content' => array(
								'type'  => 'wp-editor',
								'value' => 'Hi,<br/>
											%username% has ended trial
											<br/><br/>
											Sincerely,<br/>
											DocDirect Team<br/>
											%logo%
											',
								'attr'  => array(),
								'label' => esc_html__('Email Contents', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'size' => 'large', // small, large
								'editor_height' => 400,
							)
						)
					),
					'complete_appoinment_email' => array(
						'title' => esc_html__('Complete Appointment Email', 'docdirect'),
						'type' => 'tab',
						'options' => array(
							'complete_appoinment_subject' => array(
								'type' => 'text',
								'value' => 'Your Appointment Completed!',
								'label' => esc_html__('Subject', 'docdirect'),
								'desc' => esc_html__('Please add subject for email', 'docdirect'),
							),
							'complete_appoinment_info' => array(
								'type'  => 'html',
								'value' => '',
								'attr'  => array(),
								'label' => esc_html__('Email Settings variables', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '%username% — To display the username. <br/>
								%doctor_name% — To display the doctor name. <br/>
								%logo% — To display site logo(Optional)<br/>
								%link_review% — Link review<br/>',

							),
							'complete_appoinment_content' => array(
								'type'  => 'wp-editor',
								'value' => 'Hi %username%,<br/>
											Your appointment has complete

					                        ----------------------------------------------------<br/><br/>
					                        Review here
					                        <a href="%link_review%">Review</a><br/><br/>
											
											Sincerely,<br/>
											DocDirect Team<br/>
											%logo%
											',
								'attr'  => array(),
								'label' => esc_html__('Email Contents', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'size' => 'large', // small, large
								'editor_height' => 400,
							)
						)
					),
					'patient_confirm_email' => array(
						'title' => esc_html__('Patient Appointment Confirm', 'docdirect'),
						'type' => 'tab',
						'options' => array(
							'patient_confirm_subject' => array(
								'type' => 'text',
								'value' => 'Patient Appointment Confirm!',
								'label' => esc_html__('Subject', 'docdirect'),
								'desc' => esc_html__('Please add subject for email', 'docdirect'),
							),
							'patient_confirm_info' => array(
								'type'  => 'html',
								'value' => '',
								'attr'  => array(),
								'label' => esc_html__('Email Settings variables', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '%doctor_name% — To display the doctor name. <br/>
								%patient_name% — To display the patient name.<br/>
								%link_appointment% — Link to dashboard.<br/>
								%logo% — To display site logo(Optional)<br/>',

							),
							'patient_confirm_content' => array(
								'type'  => 'wp-editor',
								'value' => 'Hey %doctor_name%,<br/>

										%patient_name% will not arrive<br/>
										please cancel appointment<br/><br/>
				                        <a href="%link_appointment%">Dashboard</a><br/><br/>
										
										Sincerely,<br/>
										%logo%',
								'attr'  => array(),
								'label' => esc_html__('Email Contents', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'size' => 'large', // small, large
								'editor_height' => 400,
							)
						)
					),
					'patient_remind_email' => array(
						'title' => esc_html__('Patient Remind Confirm', 'docdirect'),
						'type' => 'tab',
						'options' => array(
							'patient_remind_subject' => array(
								'type' => 'text',
								'value' => 'Patient Appointment Remind!',
								'label' => esc_html__('Subject', 'docdirect'),
								'desc' => esc_html__('Please add subject for email', 'docdirect'),
							),
							'patient_remind_info' => array(
								'type'  => 'html',
								'value' => '',
								'attr'  => array(),
								'label' => esc_html__('Email Settings variables', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '%patient_name% — To display the patient name.<br/>
								%link_appointment% — Link to dashboard.<br/>
								%logo% — To display site logo(Optional)<br/>',

							),
							'patient_remind_content' => array(
								'type'  => 'wp-editor',
								'value' => 'Hi %patient_name%,<br/>

										please check your appointment<br/>
				                        <a href="%link_appointment%">Dashboard</a><br/><br/>
										
										Sincerely,<br/>
										%logo%',
								'attr'  => array(),
								'label' => esc_html__('Email Contents', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'size' => 'large', // small, large
								'editor_height' => 400,
							)
						)
					),
					'request_appoinment' => array(
						'title' => esc_html__('Request Appointment', 'docdirect'),
						'type' => 'tab',
						'options' => array(
							'request_appoinment_subject' => array(
								'type' => 'text',
								'value' => 'Request Appointment!',
								'label' => esc_html__('Subject', 'docdirect'),
								'desc' => esc_html__('Please add subject for email', 'docdirect'),
							),
							'request_appoinment_info' => array(
								'type'  => 'html',
								'value' => '',
								'attr'  => array(),
								'label' => esc_html__('Email Settings variables', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '%username% — To display the username.<br/>
								%first_name% — first_name.<br/>
								%last_name% — last_name.<br/>
								%gender% — gender.<br/>
								%phone% — phone.<br/>
								%email% — email.<br/>
								%date_of_birth% — date_of_birth.<br/>
								%insurer% — insurer.<br/>
								%hkid% — hkid.<br/>
								%message% — message.<br/>
								%logo% — To display site logo(Optional)<br/>',

							),
							'request_appoinment_content' => array(
								'type'  => 'wp-editor',
								'value' => 'Hi %username%,<br/>
											you has request appointment<br/><br/>

											First Name: %first_name%<br/>
											Last Name: %last_name%<br/>
											Gender: %gender%<br/>
											Phone Number: %phone%<br/>
											Email Address: %email%<br/>
											Date of Birth: %date_of_birth%<br/>
											Insurer: %insurer%<br/>
											HKID/Passport #: %hkid%<br/>
											Message: %message%<br/>

											<br/><br/>
											Sincerely,<br/>
											DocDirect Team<br/>
											%logo%',
								'attr'  => array(),
								'label' => esc_html__('Email Contents', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'size' => 'large', // small, large
								'editor_height' => 400,
							)
						)
					),
				)
			),
			'provider_settings' => array(
				'type' => 'tab',
				'title' => esc_html__('Provider Templates', 'docdirect'),
				'options' => array(
					'provider_appointment' => array(
						'title' => esc_html__('New Appointment', 'docdirect'),
						'type' => 'tab',
						'options' => array(
							'appointment_email'     => array(
								'type'  => 'html',
								'value' => '',
								'label' => esc_html__('', 'docdirect'),
								'desc'  => esc_html__('This template will be used for professional users to send confirmation email for appointments.', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '',
							),
							'appointment_subject' => array(
								'type' => 'text',
								'value' => 'A new Appointment has received!',
								'label' => esc_html__('Subject', 'docdirect'),
								'desc' => esc_html__('Please add subject for email', 'docdirect'),
							),
							'appointment_info' => array(
								'type'  => 'html',
								'value' => '',
								'attr'  => array(),
								'label' => esc_html__('Email Settings variables', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '%logo% — To display site logo.<br/>
											%doctor_name% — To display doctor_name.<br/>
											%user_from% — To display username, who request for appointment.<br/>
											%link% — link to appointment.<br/>
											%address% — To display Logo.<br/>
											%appointment_date% — Appointment Date<br/>
											%appointment_time% — Appointment Time.<br/>
											%phone_number% — Phone Number.<br/>
											%patient_email% — Patient Email.<br/>
											%subject% — Subject.<br/>
											%comment% — comment.<br/>
											%insurance% — insurance.<br/>
											%idcard% — idcard.<br/>
								',

							),
							'appointment_content' => array(
								'type'  => 'wp-editor',
								'value' => 'Hello<br/>

											This is confirmation that you have received a new appointment from %user_from%.<br/>
											To view detail please login to your dashboard and check it<br/><br/>

											Thank you<br/><br/>

											Sincerely,<br/>
											%logo%
											',
								'attr'  => array(),
								'label' => esc_html__('Email Contents', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'size' => 'large', // small, large
								'editor_height' => 400,
							)
						)
					),
					'confirm_booking' => array(
						'title' => esc_html__('Confirm Booking', 'docdirect'),
						'type' => 'tab',
						'options' => array(
							'confirm_email'     => array(
								'type'  => 'html',
								'value' => '',
								'label' => esc_html__('', 'docdirect'),
								'desc'  => esc_html__('This template for new registered providers. This will be default content and provider can change this text in  their dashboard.', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '',
							),
							'confirm_subject' => array(
								'type' => 'text',
								'value' => 'Your Appointment Confirmation',
								'label' => esc_html__('Subject', 'docdirect'),
								'desc' => esc_html__('Please add subject for email', 'docdirect'),
							),
							'confirm_info' => array(
								'type'  => 'html',
								'value' => '',
								'attr'  => array(),
								'label' => esc_html__('Email Settings variables', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '%customer_name% — To display customer name.<br/>
											%service% — To display appointment service.<br/>
											%provider% — To display provider name.<br/>
											%price% — To display Price.<br/>
											%logo% — To display Logo.<br/>
								',

							),
							'confirm_booking' => array(
								'type'  => 'wp-editor',
								'value' => 'Hey %customer_name%!<br/>

											This is confirmation that you have booked "%service%"<br/> with %provider%
											We will let your know regarding your booking soon.<br/><br/>

											Thank you for choosing our company.<br/><br/>

											Sincerely,<br/>
											%logo%
											',
								'attr'  => array(),
								'label' => esc_html__('Email Contents', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'size' => 'large', // small, large
								'editor_height' => 400,

								/**
								 * Also available
								 * https://github.com/WordPress/WordPress/blob/4.4.2/wp-includes/class-wp-editor.php#L80-L94
								 */
							)
						)
					),
					'approve_booking' => array(
						'title' => esc_html__('Approve Booking', 'docdirect'),
						'type' => 'tab',
						'options' => array(
							'approve_email'     => array(
								'type'  => 'html',
								'value' => '',
								'label' => esc_html__('', 'docdirect'),
								'desc'  => esc_html__('This template for new registered providers. This will be default content and provider can change this text in  their dashboard.', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '',
							),
							'approve_subject' => array(
								'type' => 'text',
								'value' => 'Your Appointment Approved',
								'label' => esc_html__('Subject', 'docdirect'),
								'desc' => esc_html__('Please add subject for email', 'docdirect'),
							),
							'approve_info' => array(
								'type'  => 'html',
								'value' => '',
								'attr'  => array(),
								'label' => esc_html__('Email Settings variables', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '%customer_name% — To display customer name.<br/>
											%doctor_name% — To display doctor name.<br/>
											%service% — To display appointment service.<br/>
											%price% — To display price.<br/>
											%address% — To display Logo.<br/>
											%appointment_date% — Appointment Date<br/>
											%appointment_time% — Appointment Time.<br/>
											%id_order% — To display Id order.<br/>
											%phone_number% — To display phone number.<br/>
											%doctor_email% — To display doctor email.<br/>
											%floor% — To display floor.<br/>
											%website% — To display website.<br/>
											%gmap_link% — To display gmap_link.<br/>
											%logo% — To display Logo.<br/>
								',

							),
							'approve_booking' => array(
								'type'  => 'wp-editor',
								'value' => 'Hey %customer_name%!<br/>

											This is confirmation that your booking regarding "%service%" with %provider% has approved by  "%doctor_name%".<br/>

											We are waiting you at "%address%" on %appointment_date% at %appointment_time%.<br/><br/><br/>

											Sincerely,<br/>
											%logo%
											',
								'attr'  => array(),
								'label' => esc_html__('Email Contents', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'size' => 'large', // small, large
								'editor_height' => 400,

								/**
								 * Also available
								 * https://github.com/WordPress/WordPress/blob/4.4.2/wp-includes/class-wp-editor.php#L80-L94
								 */
							)
						)
					),
					'cancel_booking' => array(
						'title' => esc_html__('Cancel Booking', 'docdirect'),
						'type' => 'tab',
						'options' => array(
							'cancel_email'     => array(
								'type'  => 'html',
								'value' => '',
								'label' => esc_html__('', 'docdirect'),
								'desc'  => esc_html__('This template for new registered providers. This will be default content and provider can change this text in  their dashboard.', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '',
							),
							'cancel_subject' => array(
								'type' => 'text',
								'value' => 'Your Appointment Cancelled',
								'label' => esc_html__('Subject', 'docdirect'),
								'desc' => esc_html__('Please add subject for email', 'docdirect'),
							),
							'cancel_info' => array(
								'type'  => 'html',
								'value' => '',
								'attr'  => array(),
								'label' => esc_html__('Email Settings variables', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'html'  => '%customer_name% — To display customer name.<br/>
											%service% — To display appointment service.<br/>
											%provider% — To display provider name.<br/>
											%logo% — To display Logo.<br/>
								',

							),
							'cancel_booking' => array(
								'type'  => 'wp-editor',
								'value' => 'Hi %customer_name%!<br/>

											 This is confirmation that your booking regarding "%service%" with %provider% has cancelled.<br/>

											 We are very sorry to process your booking right now.<br/><br/>

											 Sincerely,<br/>
											 %logo%
											',
								'attr'  => array(),
								'label' => esc_html__('Email Contents', 'docdirect'),
								'desc'  => esc_html__('', 'docdirect'),
								'help'  => esc_html__('', 'docdirect'),
								'size' => 'large', // small, large
								'editor_height' => 400,

								/**
								 * Also available
								 * https://github.com/WordPress/WordPress/blob/4.4.2/wp-includes/class-wp-editor.php#L80-L94
								 */
							)
						)
					),
				)
			),
		)
    )
);


