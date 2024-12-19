<?php

/**
 * This file is a part of horstoeko/zugferdvisualizer.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdvisualizer\contracts;

/**
 * Interface representing the markup renderer contract
 *
 * @category ZugferdVisualizer
 * @package  ZugferdVisualizer
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdvisualizer
 */
interface ZugferdVisualizerCodelistTransform{

	/** Translates an code to human readable doument type
	 * @param string $code
	 * @return string
	 */
	public function transformDocTypeCode(string $code): string;

	/** Translates a given unit code to human readable unit
	 * @param string $unitCode
	 * @return string
	 */
	public function transformUnit(string $unitCode): string;

	public function transformPayment(string $payment): string;

	/** formats a currency for better readability
	 * @param float $number
	 * @param string $currency
	 * @return string
	 */
	public function formatCurrency(float $number, string $currency): string;

	/** format a date
	 * @param \DateTime|null $date
	 * @return string
	 */
	public function formatDate(?\DateTime $date): string;

	/** Lookup da string and return the translation
	 * @param string $lookup
	 * @return string
	 */
	public function getString(string $lookup): string;
}
