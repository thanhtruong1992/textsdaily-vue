<?php

namespace App\Entities;

class User {
	public $id;
	public $name;
	public $email;
	public $password;
	public $remember_token;
	public $created_at;
	public $updated_at;
	function __construct($value) {
		$value = ( array ) $value;
		if (! empty ( $value ['id'] ))
			$this->setId ( $value ['id'] );
		if (! empty ( $value ['name'] ))
			$this->setName ( $value ['name'] );
		if (! empty ( $value ['email'] ))
			$this->setEmail ( $value ['email'] );
		if (! empty ( $value ['password'] ))
			$this->setPassword ( $value ['password'] );
		if (! empty ( $value ['remember_token'] ))
			$this->setRemember_token ( $value ['remember_token'] );
		if (! empty ( $value ['created_at'] ))
			$this->setCreated_at ( $value ['created_at'] );
		if (! empty ( $value ['updated_at'] ))
			$this->setUpdated_at ( $value ['updated_at'] );
	}
	private function setId($value) {
		$this->id = $value;
	}
	public function getId() {
		return $this->id;
	}
	public function setName($value) {
		$this->name = $value;
	}
	public function getName() {
		return $this->name;
	}
	public function setEmail($value) {
		$this->email = $value;
	}
	public function getEmail() {
		return $this->email;
	}
	public function setPassword($value) {
		$this->password = $value;
	}
	public function getPassword() {
		return $this->password;
	}
	public function setRemember_token($value) {
		$this->remember_token = $value;
	}
	public function getRemember_token() {
		return $this->remember_token;
	}
	public function setCreated_at($value) {
		$this->created_at = $value;
	}
	public function getCreated_at() {
		return $this->created_at;
	}
	public function setUpdated_at($value) {
		$this->updated_at = $value;
	}
	public function getUpdated_at() {
		return $this->updated_at;
	}
}