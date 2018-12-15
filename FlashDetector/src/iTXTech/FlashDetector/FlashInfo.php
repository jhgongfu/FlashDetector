<?php

/*
 * iTXTech FlashDetector
 *
 * Copyright (C) 2018 iTX Technologies
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace iTXTech\FlashDetector;

class FlashInfo{
	private $manufacturer;
	private $level;//Enterprise/consumer
	private $density;//256Gb
	private $deviceWidth;//only Micron
	private $type;//SLC MLC TLC QLC
	private $classification;//1CE 1DIE
	private $voltage;//3.3V 1.8V
	private $generation;//1 2 3 4
	private $interface;//Async/Sync ToggleDDR
	private $package;//TSOP48 BGA152 LGA52

	private $extraInfo;

	public function __construct(){
	}

	public function setManufacturer(string $m) : FlashInfo{
		$this->manufacturer = $m;
		return $this;
	}

	public function setLevel($level) : FlashInfo{
		$this->level = $level;
		return $this;
	}

	public function setDensity($density) : FlashInfo{
		$this->density = $density;
		return $this;
	}

	public function setDeviceWidth($deviceWidth) : FlashInfo{
		$this->deviceWidth = $deviceWidth;
		return $this;
	}

	public function setClassification(Classification $classification) : FlashInfo{
		$this->classification = $classification;
		return $this;
	}

	public function setType($type) : FlashInfo{
		$this->type = $type;
		return $this;
	}

	public function setGeneration($generation) : FlashInfo{
		$this->generation = $generation;
		return $this;
	}

	public function setInterface($interface) : FlashInfo{
		$this->interface = $interface;
		return $this;
	}

	public function setPackage($package) : FlashInfo{
		$this->package = $package;
		return $this;
	}

	public function setVoltage($voltage) : FlashInfo{
		$this->voltage = $voltage;
		return $this;
	}

	public function setExtraInfo(array $extraInfo) : FlashInfo{
		$this->extraInfo = $extraInfo;
		return $this;
	}

	public function __toString(){
		$reflectionClass = new \ReflectionClass($this);
		$properties = $reflectionClass->getProperties();
		$info = [];
		foreach($properties as $property){
			if(is_object($this->{$property->getName()})){
				$info[$property->getName()] = $this->{$property->getName()}->getArray();
			} else{
				$info[$property->getName()] = $this->{$property->getName()};
			}
		}
		return json_encode($info);
	}
}