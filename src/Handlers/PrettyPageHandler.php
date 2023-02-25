<?php

declare(strict_types=1);

namespace Fi1a\ErrorHandler\Handlers;

use Fi1a\ErrorHandler\InspectorInterface;
use Fi1a\Http\CookieInterface;

/**
 * Html обработчик
 */
class PrettyPageHandler implements HandlerInterface
{
    /**
     * @var array<string, string[]>|array<string, string>
     */
    protected $blacklist = [
        '$_GET' => [],
        '$_POST' => [],
        '$_FILES' => [],
        '$_COOKIE' => [],
        '$_SESSION' => [],
        '$_SERVER' => [],
        '$_ENV' => [],
    ];

    /**
     * @param array<string, string[]>|array<string, string> $blacklist
     */
    public function __construct(array $blacklist = [])
    {
        $this->blacklist = $blacklist;
    }

    /**
     * @inheritDoc
     */
    public function handle(InspectorInterface $inspector): void
    {
        $this->sendResponse();
        if ((int) ini_get('display_errors') === 1) {
            $this->includeTemplate($inspector, $this->getInfo());
        }
    }

    /**
     * Отправляет заголовки и очищает буффер
     */
    protected function sendResponse(): void
    {
        ob_end_clean();
        buffer()->clear();
        response(response()->withStatus(500));
        buffer()->sendHeaders(response());
    }

    /**
     * Подключает шаблон
     *
     * @param array<string, string[]> $info
     */
    protected function includeTemplate(
        InspectorInterface $inspector,
        array $info
    ): void {
        require __DIR__ . '/../../resources/pretty-page/pretty.php';
    }

    /**
     * Возвращает информацию
     *
     * @return array<string, string[]>
     */
    protected function getInfo(): array
    {
        $request = request();

        $cookies = [];
        /** @var CookieInterface $cookie */
        foreach ($request->cookies() as $cookie) {
            $cookieName = $cookie->getName();
            if ($cookieName) {
                $cookies[$cookieName] = $cookie->getValue();
            }
        }

        session()->open();
        $session = session()->getValues();

        return [
            '$_GET' => $this->taking('$_GET', $request->query()->getArrayCopy()),
            '$_POST' => $this->taking('$_POST', $request->post()->getArrayCopy()),
            '$_FILES' => $this->taking('$_FILES', $request->files()->getArrayCopy()),
            '$_COOKIE' => $this->taking('$_COOKIE', $cookies),
            '$_SESSION' => $this->taking('$_SESSION', $session
                ? $session->getArrayCopy()
                : []),
            '$_SERVER' => $this->taking('$_GET', $request->server()->getArrayCopy()),
            '$_ENV' => $this->taking('$_GET', $_ENV),
        ];
    }

    /**
     * Возвращает данные для отображения
     *
     * @param mixed[] $data
     *
     * @return array<string, string>
     */
    protected function taking(string $name, array $data): array
    {
        $result = [];
        $blacklist = [];
        if (isset($this->blacklist[$name])) {
            $blacklist = $this->blacklist[$name];
            if (!is_array($blacklist)) {
                $blacklist = (array) $blacklist;
            }
        }

        /** @var mixed $value */
        foreach ($data as $key => $value) {
            if (!in_array($key, $blacklist)) {
                $result[(string) $key] = var_export($value, true);

                continue;
            }
            $result[(string) $key] = '***';
        }

        return $result;
    }
}
