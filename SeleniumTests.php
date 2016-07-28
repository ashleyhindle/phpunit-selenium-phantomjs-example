<?php

class SeleniumTests extends PHPUnit_Extensions_Selenium2TestCase {

    public function setUp() {
        $this->setHost('192.168.50.50'); // Our Vagrant private IP, so we can run the tests from our local machine
        $this->setPort(4444);

        $this->setBrowser('phantomjs');
	$this->setBrowserUrl('https://twitter.com');

	// So the window isn't too small it hides inputs (CSS is responsive)
	$this->prepareSession()->currentWindow()->size(array(
		'width' => 1024,
		'height' => 768,
	));
    }

    public function tearDown()
    {
	$this->closeWindow();
    }


    /** @test */
    public function it_has_correct_size()
    {
        $size = $this->prepareSession()->currentWindow()->size();
        $this->assertEquals(1024, $size['width']);
        $this->assertEquals(768, $size['height']);
    }

    /** @test */
    public function it_has_a_sign_up_button()
    {
        $this->url('/');

        try {
            $el = $this->byCssSelector(".Button.StreamsSignUp.js-nav.js-signup");
        } catch (PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e) {
	    $this->fail('This element should exist.');
            return;
        }
    }

    /** @test */
    public function we_can_search()
    {
        $this->url('/');

	$this->byCssSelector('.Icon.Icon--search')->click();
	$searchInput = $this->byId('search-query');
	$searchInput->value('cheese burger');
	$searchInput->submit();

	$that = $this;
        // Wait until the AJAX loads the results (as we submitted the search form just above, so our chrome is currently AJAXing the results)
        $this->waitUntil(function() use($that) {
                return $that->byCssSelector('.AdaptiveSearchTitle-title');
        }, 3000);

	$this->screenshot(__DIR__ . '/screenshot.png');

	$title = $this->byCssSelector('.AdaptiveSearchTitle-title');
        $this->assertEquals('cheese burger', $title->text());
    }

    private function screenshot($filename)
    {
	$filedata = $this->currentScreenshot();
        file_put_contents($filename, $filedata);
    }
}
