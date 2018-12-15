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

namespace iTXTech\FlashDetector\Decoder;

use iTXTech\FlashDetector\FlashInfo;
use iTXTech\FlashDetector\Property\Classification;
use iTXTech\FlashDetector\Property\FlashInterface;
use iTXTech\SimpleFramework\Util\StringUtil;

class Intel extends Decoder{
	public static function getName() : string{
		return "Intel";
	}

	public static function check(string $partNumber) : bool{
		$code = substr($partNumber, 0, 2);
		if(in_array($code, ["JS", "PF", "29", "X2"])){
			return true;
		}
		return false;
	}

	public static function decode(string $partNumber) : FlashInfo{
		$flashInfo = (new FlashInfo($partNumber))->setManufacturer(self::getName());
		$extra = [
			"wafer" => false
		];
		if(StringUtil::startsWith($partNumber, "X")){
			$extra["wafer"] = true;
			$partNumber = substr($partNumber, 1, strlen($partNumber));
		}elseif(StringUtil::startsWith($partNumber, "JS") or
			StringUtil::startsWith($partNumber, "PF")){
			$partNumber = substr($partNumber, 2, strlen($partNumber));
		}
		$partNumber = substr($partNumber, 3, strlen($partNumber));
		$flashInfo->setLevel("NAND Flash")
			->setDensity(self::getOrDefault($density = self::shiftChars($partNumber, 3), [
				"08G" => "1 GB",
				"16G" => "2 GB",
				"32G" => "4 GB",
				"64G" => "8 GB",
				"16B" => "16 GB",
				"32B" => "32 GB",
				//TODO: 02T
			]))
			->setDeviceWidth(self::getOrDefault(self::shiftChars($partNumber, 2), [
				"08" => "x8",
				"16" => "x16"
			]));
		if(((int) $density{2}) > 0){//same as Micron
			return Micron::decode($flashInfo->getPartNumber());
		}
		$classification = self::getOrDefault(self::shiftChars($partNumber, 1), [
			"A" => [1, 1, 1, true],//Die, CE, RnB, I/O Common/Separate (Sync/Async only)
			"B" => [2, 1, 1, true],
			"C" => [2, 2, 2, true],
			"D" => [2, 2, 2, true],
			"E" => [2, 2, 2, false],
			"F" => [4, 2, 2, true],
			"G" => [4, 2, 2, false],
			"J" => [4, 4, 4, true],
			"K" => [8, 4, 4, false]
		], [-1, -1, -1, false]);
		$flashInfo->setClassification(new Classification(
			$classification[1], Classification::UNKNOWN_PROP, $classification[2], $classification[0]))
			->setInterface((new FlashInterface(false))->setAsync(true)->setSync($classification[3]))
			->setVoltage(self::getOrDefault(self::shiftChars($partNumber, 1), [
				"A" => "3.3V (2.70V-3.60V)",
				"B" => "1.8V (1.70V-1.95V)"
				//TODO: C
			]))
			->setType(self::getOrDefault(self::shiftChars($partNumber, 1), [
				"N" => "SLC",
				"M" => "MLC",
				"T" => "TLC", //TODO: Confirm
			]))
			->setLithography(self::getOrDefault(self::shiftChars($partNumber, 1), [
				"A" => "90 nm",
				"B" => "72 nm",
				"C" => "50 nm",
				"D" => "34 nm",
				"E" => "25 nm",
				//TODO: confirm
				"F" => "20 nm",
				"G" => "3D L06"
			]))
			->setGeneration(self::shiftChars($partNumber, 1));


		return $flashInfo;
	}
}
