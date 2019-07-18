<?php


namespace App\Service;


use Michelf\MarkdownInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Security\Core\Security;

class MarkdownHelper
{
    /**
     * @var AdapterInterface
     */
    private $cache;

    /**
     * @var MarkdownInterface
     */
    private $markdown;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $isDebug;

    /**
     * @var Security
     */
    private $security;

    /**
     * MarkdownHelper constructor.
     * @param AdapterInterface $cache
     * @param MarkdownInterface $markdown
     * @param LoggerInterface $markdownLogger
     * @param bool $isDebug
     * @param Security $security
     */
    public function __construct(AdapterInterface $cache, MarkdownInterface $markdown, LoggerInterface $markdownLogger, bool $isDebug, Security $security)
    {
        $this->cache = $cache;
        $this->markdown = $markdown;
        $this->logger = $markdownLogger;
        $this->isDebug = $isDebug;
        $this->security = $security;
    }

    public function parse(string $text)
    {
        if (stripos($text, 'bacon') !== false) {
            $this->logger->info('Mmmmh bacon', [
                'user' => $this->security->getUser(),
            ]);
        }

        if ($this->isDebug) {
            return $this->markdown->transform($text);
        }

        $item = $this->cache->getItem('markdown_' . md5($text));
        if (!$item->isHit()) {
            $item->set($this->markdown->transform($text));
            $this->cache->save($item);
        }

        return $item->get();
    }
}