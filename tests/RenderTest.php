<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Render\Xmler;
use Render\Render;

class RenderTest extends TestCase
{
    public function test()
    {
        $render = new Render(__DIR__ . '/data/theme');

        $render->managerTags->addTag('theme.card', 'native/card.html');
        $render->managerTags->addTag('theme.card-text', 'native/card_text.html');
        $render->managerTags->addTag('theme.card-header', 'native/card_header.html');
        $render->managerTags->addTag('theme.card-body', 'native/card_body.html');
        $render->managerTags->addTag('theme.card-footer', 'native/card_footer.html');
        $render->managerTags->addTag('theme.card-action', 'native/card_action.html');

        $render->managerTags->addTag(
            'theme.button',
            'native/button.html',
            function (array $attributes, Xmler $xmler) {
                if (array_key_exists('color', $attributes)) {
                    $xmler->addClass($attributes['color']);
                }
            }
        );

        $render->loadTemplate(file_get_contents(__DIR__ . '/data/templates/test2.xml'));
        file_put_contents(__DIR__ . '/data/output/output.html', $render->render([
            'title' => 'This is title',
            'content' =>
                'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci architecto molestias' .
                'perferendis velit! Ab amet deleniti odio. A id incidunt molestias neque, odit perspiciatis quam sint?' .
                'Debitis distinctio facere saepe.',
            'action1' => 'Press me'
        ]));
        $this->assertTrue(true);
    }
}
