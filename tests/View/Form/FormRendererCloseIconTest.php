<?php

declare(strict_types=1);

namespace Ngaje\Scaffold\Tests\View\Form;

use Ngaje\Scaffold\Url;
use Ngaje\Scaffold\View\Form\FormBase;
use Ngaje\Scaffold\View\Form\FormRenderer;
use PHPUnit\Framework\TestCase;

final class FormRendererCloseIconTest extends TestCase
{
    public function testSharedFormMessageUsesTheLegacyGreenCloseIcon(): void
    {
        $form = $this->getMockBuilder(FormBase::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getString'])
            ->getMock();
        $form->method('getString')->willReturnMap([
            ['close', 'global', 'Close'],
            ['bare_entry_url', 'routing', '/index.php?option=com_hra'],
        ]);
        $form->css_class = 'supplier';
        $form->message = 'Parent Record Updated';

        $renderer = new FormRenderer($form, new Url('https://example.test/form'));

        ob_start();
        $renderer->renderMessage();
        $html = (string) ob_get_clean();

        self::assertStringContainsString(
            '/index.php?option=com_hra&resource=image&id=close_green.png',
            $html,
        );
        self::assertStringNotContainsString(
            '/index.php?option=com_hra&resource=image&id=close.png',
            $html,
        );
        self::assertStringContainsString('Parent Record Updated', $html);
    }
}
