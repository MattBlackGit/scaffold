<?php

declare(strict_types=1);

namespace Ngaje\Scaffold\Tests\View\Form;

use Ngaje\Scaffold\ICms;
use Ngaje\Scaffold\Language;
use Ngaje\Scaffold\View\Form\FieldBase;
use Ngaje\Scaffold\View\Form\FieldRenderer;
use PHPUnit\Framework\TestCase;

final class FieldRendererHelpIconTest extends TestCase
{
    public function testSharedFormHelpIconUsesTheLegacyTwentyFivePixelInlineSize(): void
    {
        $language = new HelpIconLanguage();
        $field = new FieldBase($language, 'text', 'example', 'example');
        $field->help_text = 'Example help';
        $renderer = new TestableFieldRenderer($field, $language, new NullCms());

        ob_start();
        $renderer->renderHelpTextForTest();
        $html = (string) ob_get_clean();

        self::assertStringContainsString('resource=image&amp;id=help.png', htmlspecialchars($html));
        self::assertStringContainsString('style="width: 25px; height: 25px; border: 0;"', $html);
        self::assertSame(1, substr_count($html, 'style="width: 25px; height: 25px; border: 0;"'));
    }
}

final class TestableFieldRenderer extends FieldRenderer
{
    public function renderHelpTextForTest(): void
    {
        $this->renderHelpText();
    }
}

final class HelpIconLanguage extends Language
{
    public function __construct()
    {
    }

    public function __get($property)
    {
        if ($property === 'routing') {
            return ['bare_entry_url' => '/index.php?option=com_hra'];
        }

        if ($property === 'global') {
            return ['help' => 'Help'];
        }

        return [];
    }
}

final class NullCms implements ICms
{
    public function getCurrentUserId() {}
    public function addStylesheet($css_file) {}
    public function addJavascript($js_file) {}
    public function addHeadContent($content) {}
    public function registerUser($username, $password, $first_name, $last_name, $email_address) {}
    public function deleteUser($user_id) {}
    public function login($user, $password, $remember, $check_token = false) {}
    public function logout() {}
    public function sendEmail($from_address, $from_name, $recipient, $subject, $body, $cc = null, $bcc = null, $attachments = [], $reply_to = null, $reply_to_name = null) {}
    public function getPath() {}
}
