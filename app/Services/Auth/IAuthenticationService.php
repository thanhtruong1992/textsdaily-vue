<?php

namespace App\Services\Auth;

interface IAuthenticationService {
	public function register($request);
	public function getAllUser();
	public function loginUser($request);
	public function loginOtherRoleWithId($clientId);
	public function getUserInfo( $idUser );
	public function getSenderList($userId);
	public function addNewSender($userId, $sender);
	public function getAllUserGroup3($userID = null);
	public function getAllUserGroup2($userID = null);
	public function getChildrens($userID);
	public function getAllUserChildrenByParent($userID = null, $toArray = true);
	public function forgotPassword ($request);
	public function checkTokenForgotPassword ($request);
	public function resetPassword ($request);
	public function checkUsername ($request);
}