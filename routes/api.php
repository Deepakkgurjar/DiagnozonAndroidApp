<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register','Api\RegisterController@register');

Route::post('login','Api\LoginController@login');

Route::post('profile_complition','Api\ProfileController@registerProfileDetails')->middleware('auth:api');

Route::post('add-blood-group','Api\ProfileController@addBloodGroup')->middleware('auth:api');

Route::post('service_register','Api\ServiceController@serviceRegister')->middleware('auth:api');

Route::post('book_appointment','Doctor\AppointmentController@bookAppointment')->middleware('auth:api');

Route::post('doctor_schedule','Doctor\ScheduleController@makeDoctorSchedule')->middleware('auth:api');

Route::post('slot-distribution-doctor','Doctor\ScheduleController@slotDistrubution')->middleware('auth:api');

Route::post('slot-distribution-lab','Laboratory\ScheduleController@slotDistrubution')->middleware('auth:api');

Route::post('patient_upload_prescription','Patient\PatientController@uploadPrescription')->middleware('auth:api');

Route::post('all-patient-prescription','Patient\PatientController@allPatientPrescription')->middleware('auth:api');

Route::post('all_results','Patient\PatientController@allResultSearch')->middleware('auth:api');

Route::post('all_doctors','Patient\PatientController@allDoctors')->middleware('auth:api');

Route::post('all_laboratories','Patient\PatientController@allLabs')->middleware('auth:api');

Route::post('global_search','Patient\PatientController@globalSearch')->middleware('auth:api');

Route::post('doctor_reschedule','Doctor\ScheduleController@doctorReSchedule')->middleware('auth:api');


Route::post('lab_schedule','Laboratory\ScheduleController@makeLabSchedule')->middleware('auth:api');

Route::post('lab_reschedule','Laboratory\ScheduleController@labReSchedule')->middleware('auth:api');

Route::post('lab_slots','Laboratory\ScheduleController@slotDistrubution')->middleware('auth:api');

Route::post('book-appointment-in-lab','Laboratory\AppointmentController@bookAppointmentLab')->middleware('auth:api');

Route::post('service-total-amount','Patient\PatientController@totalAmount')->middleware('auth:api');

Route::post('recent-services','Patient\PatientController@recentServices')->middleware('auth:api');

Route::post('recent-doctors','Patient\PatientController@recentDoctors')->middleware('auth:api');

Route::post('my-services','Api\ServiceController@myServices')->middleware('auth:api');

Route::post('my-profile','Api\ProfileController@myProfile')->middleware('auth:api');

Route::post('upcoming-appointment','Doctor\DoctorController@upcomingAppointment')->middleware('auth:api');

Route::post('upcoming-test','Laboratory\LaboratoryController@upcomingTest')->middleware('auth:api');

Route::post('my-schedule','Patient\PatientController@mySchedule')->middleware('auth:api');

Route::post('patient-records','Doctor\DoctorController@patientRecords')->middleware('auth:api');

Route::post('view-prescription','Doctor\DoctorController@viewPrescription')->middleware('auth:api');

Route::post('upload-prescription-bydoctor','Doctor\DoctorController@uploadPrescriptionbyDoctor')->middleware('auth:api');

Route::post('appointment-calculator','Patient\PatientController@appointmentCalculator')->middleware('auth:api');

Route::post('doctor-detail','Api\ProfileController@doctorDetail')->middleware('auth:api');

Route::post('laboratory-detail','Api\ProfileController@laboratoryDetail')->middleware('auth:api');

Route::post('patient-detail','Api\ProfileController@patientDetail')->middleware('auth:api');

Route::post('to-day-appointments','Doctor\DoctorController@toDayAppointments')->middleware('auth:api');

Route::post('to-day-tests','Laboratory\LaboratoryController@toDayTests')->middleware('auth:api');

Route::post('cancel-appointment','Patient\PatientController@cancelAppointment')->middleware('auth:api');

Route::post('cancel-test','Patient\PatientController@cancelTest')->middleware('auth:api');

Route::post('re-plan-appointment','Patient\PatientController@rePlanAppointment')->middleware('auth:api');

Route::post('re-plan-test','Patient\PatientController@rePlanTest')->middleware('auth:api');

Route::post('appointment-done','Doctor\DoctorController@appointmentDone')->middleware('auth:api');

Route::post('otp-generate','Api\LoginController@otpGenerate');

Route::post('change-password','Api\LoginController@changePassword');

Route::post('upload-report','Laboratory\LaboratoryController@uploadReport')->middleware('auth:api');

Route::post('view-report','Patient\PatientController@viewReport')->middleware('auth:api');

// Dropdown api's for doctor app

Route::post('clinic-types','Doctor\DoctorController@clinicTypes');

Route::post('higest-qualification','Doctor\DoctorController@higestQualification');

Route::post('specilization','Doctor\DoctorController@specilizations');

Route::post('delete-services','Api\ServiceController@deleteServices')->middleware('auth:api');

Route::post('edit-services','Api\ServiceController@editService')->middleware('auth:api');

Route::post('edit-profile','Api\ProfileController@editProfile')->middleware('auth:api');
Route::post('time-table','Api\ServiceController@time_table')->middleware('auth:api');