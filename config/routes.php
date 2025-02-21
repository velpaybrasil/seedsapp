<?php

use App\Core\Router;

// Prayer Requests Routes
Router::get('intercessao', 'PrayerRequestController::index');
Router::post('intercessao', 'PrayerRequestController::store');
Router::post('intercessao/(:num)/status', 'PrayerRequestController::updateStatus/$1');

// Visitor Forms Routes
Router::get('visitor-forms', 'VisitorFormsController::index');
Router::get('visitor-forms/create', 'VisitorFormsController::create');
Router::post('visitor-forms', 'VisitorFormsController::store');
Router::get('visitor-forms/(:num)', 'VisitorFormsController::show/$1');
Router::get('visitor-forms/(:num)/edit', 'VisitorFormsController::edit/$1');
Router::post('visitor-forms/(:num)', 'VisitorFormsController::update/$1');
Router::post('visitor-forms/(:num)/delete', 'VisitorFormsController::delete/$1');

// Visitor Form Submission Routes
Router::get('f/(:any)', 'VisitorFormsController::showPublic/$1');
Router::post('f/(:any)/submit', 'VisitorFormSubmissionController::submit/$1');
Router::get('f/(:any)/success', 'VisitorFormSubmissionController::success/$1');

// Visitor Form Submissions Management Routes
Router::get('visitor-forms/(:num)/submissions', 'VisitorFormsController::submissions/$1');
Router::get('visitor-forms/(:num)/submissions/(:num)', 'VisitorFormsController::viewSubmission/$1/$2');
Router::post('visitor-forms/(:num)/submissions/(:num)/delete', 'VisitorFormSubmissionController::delete/$1/$2');
