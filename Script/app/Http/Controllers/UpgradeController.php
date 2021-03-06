<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Categories;
use App\Models\User;
use App\Models\AdminSettings;
use App\Models\Campaigns;
use App\Models\Donations;

class UpgradeController extends Controller {

	public function update($version) {

		$upgradeDone = '<h2 style="text-align:center; margin-top: 30px; font-family: Arial, san-serif;color: #4BBA0B;">'.trans('admin.upgrade_done').' <a style="text-decoration: none; color: #F50;" href="'.url('/').'">'.trans('error.go_home').'</a></h2>';

		//<---------------------------- Version 1.2
		if( $version == '1.2' ) {

			 $category = Categories::first();

			if( isset($category->image) ) {
				return redirect('/');
			} else {

				Schema::table('categories', function($table){
					$table->string('image', 200)->after('mode');
				 });

				return $upgradeDone;
			}
		}//<------------------------ Version 1.2

		//<-------------------------- Version 1.6
		if( $version == '1.6' ) {

			$admin = AdminSettings::first();

			if( isset($admin->auto_approve_campaigns) ) {
				return redirect('/');
			} else {

				Schema::table('admin_settings', function($table){
					$table->enum('auto_approve_campaigns', ['0', '1'])->default('1')->after('fee_donation');
				 });

				return $upgradeDone;
			}
		}//<------------------------- Version 1.6

		//<------------------------ Version 1.7
		if( $version == '1.7' ) {

			$admin = AdminSettings::first();
			$campaigns = Campaigns::first();

			if( isset($admin->max_donation_amount) && isset( $campaigns->featured ) ) {
				return redirect('/');
			} else {

				Schema::table('admin_settings', function($table){
					$table->unsignedInteger('max_donation_amount')->after('stripe_public_key');
				 });

				 Schema::table('campaigns', function($table){
				 	$table->enum('featured', ['0', '1'])->default('0')->after('categories_id');
				 });

				return $upgradeDone;
			}
		}//<---------------------- Version 1.7

		//<------------------------ Version 1.8
		if( $version == '1.8' ) {


			if( Schema::hasTable('campaigns_reported') ) {
				return redirect('/');
			} else {

				 Schema::create('campaigns_reported', function ($table) {

				    $table->engine = 'InnoDB';

				    $table->increments('id');
					$table->unsignedInteger('user_id');
					$table->unsignedInteger('campaigns_id');
					$table->timestamp('created_at');
				});

				return $upgradeDone;
			}
		}//<---------------------- Version 1.8


		//<------------------------ Version 1.9.1
		if( $version == '1.9.1' ) {


			if( Schema::hasTable('likes') ) {
				return redirect('/');
			} else {

				 Schema::create('likes', function ($table) {

				    $table->engine = 'InnoDB';

				    $table->increments('id');
					$table->unsignedInteger('user_id');
					$table->unsignedInteger('campaigns_id');
					$table->enum('status', ['0', '1'])->default('1');
					$table->timestamp('date');
				});

				return $upgradeDone;
			}
		}//<---------------------- Version 1.9.1

		//<------------------------ Version 2.0
		if( $version == '2.0' ) {

			$query = Campaigns::first();

			if( isset($query->deadline) ) {
				return redirect('/');
			} else {

				Schema::table('campaigns', function($table){
					$table->string('deadline', 200)->after('featured');
				 });

				return $upgradeDone;
			}
		}//<------------------------- Version 2.0

		//<------------------------ Version 2.2
		if( $version == '2.2' ) {

			$query = Donations::first();
			$admin = AdminSettings::first();

			if( Schema::hasTable('rewards') && isset($query->rewards_id) ) {
				return redirect('/');
				exit;
			} else {

				  Schema::create('rewards', function ($table) {

				  $table->engine = 'InnoDB';

				  $table->increments('id');
					$table->unsignedInteger('campaigns_id');
					$table->string('title', 200);
					$table->unsignedInteger('amount');
					$table->text('description');
					$table->unsignedInteger('quantity');
					$table->string('delivery', 200);
				});

				Schema::table('donations', function($table){
					$table->unsignedInteger('rewards_id')->after('anonymous');
				});

			}//ELSE

			if( isset($admin->enable_paypal)
		 		&& isset( $admin->enable_stripe )
				&& isset( $admin->enable_bank_transfer )

				&& isset( $admin->bank_swift_code )
				&& isset( $admin->account_number )
				&& isset( $admin->branch_name )
				&& isset( $admin->branch_address )
				&& isset( $admin->account_name )
				&& isset( $admin->iban )

				&& isset( $query->bank_swift_code )
				&& isset( $query->account_number )
				&& isset( $query->branch_name )
				&& isset( $query->branch_address )
				&& isset( $query->account_name )
				&& isset( $query->iban )
				&& isset( $query->approved )
			 ) {
				return redirect('/');
				exit;
			} else {

				Schema::table('admin_settings', function($table){
					$table->enum('enable_paypal', ['0', '1'])->default('0')->after('max_donation_amount');
					$table->enum('enable_stripe', ['0', '1'])->default('0');
					$table->enum('enable_bank_transfer', ['0', '1'])->default('0');

					$table->string('bank_swift_code', 250);
					$table->string('account_number', 250);
					$table->string('branch_name', 250);
					$table->string('branch_address', 250);
					$table->string('account_name', 250);
					$table->string('iban', 250);
				 });

				 Schema::table('donations', function($table){
 					$table->string('bank_swift_code', 250)->after('rewards_id');
 					$table->string('account_number', 250);
 					$table->string('branch_name', 250);
 					$table->string('branch_address', 250);
 					$table->string('account_name', 250);
 					$table->string('iban', 250);
 					$table->enum('approved', ['0', '1'])->default('1');
 				 });

			}

			return $upgradeDone;


		}//<------------------------- Version 2.2

		//<<---- Version 2.6 ----->>
		if( $version == '2.6' ) {

			 // Add Link to Pages Terms and Privacy
			 if( !Schema::hasColumn('admin_settings', 'link_terms', 'link_privacy', 'date_format', 'currency_position' ) ) {
				 Schema::table('admin_settings', function($table){
 					$table->string('link_terms', 200)->after('iban');
					$table->string('link_privacy', 200)->after('iban');
					$table->string('date_format', 200)->after('iban');
					$table->enum('currency_position', ['left', 'right'])->default('left');
 				 });
			 }// <<--- End Add Link to Pages Terms and Privacy


					return $upgradeDone;

		}//<<---- Version 2.6 ----->>

		//<---------------------------- Version 2.8
		if( $version == '2.8' ) {

			 $user = User::first();

			if( isset($user->username)
		 		&& isset($user->oauth_provider)
			  && isset($user->oauth_uid)){
				return redirect('/');
			} else {

				Schema::table('users', function($table){
					$table->string('username', 50)->after('bank');
					$table->string('oauth_provider', 200)->after('bank');
					$table->string('oauth_uid', 200)->after('bank');
				 });

				return $upgradeDone;
			}
		}//<------------------------ Version 2.8

	}//<--- End Method

}
