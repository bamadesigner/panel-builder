<?php

namespace ModularContent\Fields;

use Codeception\TestCase\WPTestCase;

class Number_Test extends WPTestCase {

	public function test_blueprint() {
		$label        = __CLASS__ . '::' . __FUNCTION__;
		$name         = __FUNCTION__;
		$description  = __FUNCTION__ . ':' . __LINE__;
		$default      = __LINE__;
		$min          = 10;
		$max          = 200;
		$step         = 0.1;
		$unit_display = 'px';

		$field = new Number( [
			'label'        => $label,
			'name'         => $name,
			'description'  => $description,
			'default'      => $default,
			'min'          => $min,
			'max'          => $max,
			'step'         => $step,
			'unit_display' => $unit_display,
			'input_width'  => 3,
			'layout'       => 'compact',
		] );

		$blueprint = $field->get_blueprint();

		$expected = [
			'type'         => 'Number',
			'label'        => $label,
			'name'         => $name,
			'description'  => $description,
			'strings'      => [],
			'default'      => $default,
			'min'          => $min,
			'max'          => $max,
			'step'         => $step,
			'unit_display' => $unit_display,
			'input_width'  => 3,
			'layout'       => 'compact',
		];

		$this->assertEquals( $expected, $blueprint );
	}

	public function test_input_width_error() {
		$valid = true;

		try {
			new Number( [
				'input_width' => 14,
			] );
		} catch ( \LogicException $e ) {
			$valid = false;
		}

		$this->assertFalse( $valid );
	}

	public function test_layout_error() {
		$valid = true;

		try {
			new Number( [
				'layout' => 'foobar',
			] );
		} catch ( \LogicException $e ) {
			$valid = false;
		}

		$this->assertFalse( $valid );
	}

	public function test_input_and_layout_error() {
		$valid = true;

		try {
			new Number( [
				'layout'      => 'full',
				'input_width' => 12,
			] );
		} catch ( \LogicException $e ) {
			$valid = false;
		}

		$this->assertFalse( $valid );
	}
}