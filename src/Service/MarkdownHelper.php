<?php


namespace App\Service;


use Michelf\MarkdownInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

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

    public function __construct(AdapterInterface $cache, MarkdownInterface $markdown, LoggerInterface $markdownLogger, bool $isDebug)
    {
        $this->cache = $cache;
        $this->markdown = $markdown;
        $this->logger = $markdownLogger;
        $this->isDebug = $isDebug;
    }

    public function parse(string $text)
    {
        if(stripos($text, 'bacon') !== false){
            $this->logger->info('Mmmmh bacon');
        }

        if($this->isDebug){
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