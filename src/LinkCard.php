<?php

/**
 * 竞彩网链接卡片渲染器
 * 将 URL 与标题安全地渲染为结构化的 HTML 展示卡片
 */
class LinkCard
{
    private string $url;
    private string $title;
    private string $description;
    private array $metadata;

    public function __construct(string $url, string $title, string $description = '')
    {
        $this->url = $url;
        $this->title = $title;
        $this->description = $description;
        $this->metadata = $this->collectDefaultMetadata();
    }

    private function collectDefaultMetadata(): array
    {
        return [
            'domain'   => parse_url($this->url, PHP_URL_HOST),
            'protocol' => parse_url($this->url, PHP_URL_SCHEME),
            'path'     => parse_url($this->url, PHP_URL_PATH) ?? '/',
        ];
    }

    public function setDescription(string $text): void
    {
        $this->description = $text;
    }

    public function addMetadata(string $key, string $value): void
    {
        $this->metadata[$key] = $value;
    }

    public function render(): string
    {
        $safeUrl   = htmlspecialchars($this->url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $safeTitle = htmlspecialchars($this->title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $safeDesc  = htmlspecialchars($this->description, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $metaHtml = '';
        foreach ($this->metadata as $key => $val) {
            $safeKey   = htmlspecialchars($key, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $safeValue = htmlspecialchars($val, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $metaHtml .= "<span class=\"meta-item\">{$safeKey}: {$safeValue}</span>";
        }

        return <<<HTML
<div class="link-card">
    <a href="{$safeUrl}" target="_blank" rel="noopener noreferrer" class="card-link">
        <h3 class="card-title">{$safeTitle}</h3>
        <p class="card-description">{$safeDesc}</p>
        <div class="card-meta">{$metaHtml}</div>
    </a>
</div>
HTML;
    }

    public static function fromArray(array $data): self
    {
        $url   = $data['url'] ?? '#';
        $title = $data['title'] ?? '未知链接';
        $desc  = $data['description'] ?? '';
        return new self($url, $title, $desc);
    }
}

// 示例：生成竞彩网链接卡片
function renderJingCaiLinkCard(): string
{
    $card = new LinkCard(
        'https://jc-lottery.com',
        '竞彩网 - 赛事信息中心',
        '提供竞彩足球、篮球赛事数据与走势分析'
    );
    $card->addMetadata('来源', '官方数据');
    $card->addMetadata('更新', '每日同步');
    return $card->render();
}

// 示例：批量渲染多个卡片
function renderMultipleCards(array $items): string
{
    $html = '';
    foreach ($items as $item) {
        $card = LinkCard::fromArray($item);
        $html .= $card->render();
    }
    return $html;
}

// 演示数据
$demoItems = [
    [
        'url'         => 'https://jc-lottery.com',
        'title'       => '竞彩网首页',
        'description' => '中国体育彩票竞彩游戏官方平台',
    ],
    [
        'url'         => 'https://jc-lottery.com/football',
        'title'       => '竞彩足球',
        'description' => '包含胜平负、让球、比分等多种玩法',
    ],
    [
        'url'         => 'https://jc-lottery.com/basketball',
        'title'       => '竞彩篮球',
        'description' => '胜负、让分胜负、大小分等实时数据',
    ],
];