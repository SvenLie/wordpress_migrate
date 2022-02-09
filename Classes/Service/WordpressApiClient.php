<?php

namespace SvenLie\WordpressMigrate\Service;

use GuzzleHttp\Exception\RequestException;
use SvenLie\WordpressMigrate\Domain\Model\WordpressApi\Category;
use SvenLie\WordpressMigrate\Domain\Model\WordpressApi\Comment;
use SvenLie\WordpressMigrate\Domain\Model\WordpressApi\Page;
use SvenLie\WordpressMigrate\Domain\Model\WordpressApi\Post;
use SvenLie\WordpressMigrate\Domain\Model\WordpressApi\Tag;
use TYPO3\CMS\Core\Http\RequestFactory;

class WordpressApiClient
{
    protected string $wordpressUri;
    final const ITEMS_PER_PAGE = 100;

    public function __construct(RequestFactory $requestFactory)
    {
        $this->requestFactory = $requestFactory;
    }

    /**
     * @return string
     */
    public function getWordpressUri(): string
    {
        return $this->wordpressUri;
    }

    /**
     * @param string $wordpressUri
     */
    public function setWordpressUri(string $wordpressUri): void
    {
        $this->wordpressUri = $wordpressUri;
    }

    /**
     * @return Post[]|bool
     */
    public function getPosts(): iterable | bool
    {
        $requestUri = $this->getWordpressUri() . "/wp-json/wp/v2/posts";
        $currentPage = 0;
        $posts = [];

        do {
            $currentPage++;
            try {
                $response = $this->requestFactory->request($requestUri . "?per_page=" . $this::ITEMS_PER_PAGE . "&_fields=id,slug,title,content,excerpt,categories,tags,date&page=" . $currentPage,'GET');
            } catch (RequestException $e) {
                return false;
            }

            if ($response->getStatusCode() === 200) {
                $content = json_decode($response->getBody()->getContents());
                for ($i = 0; $i < count($content); $i++) {
                    $post = new Post();
                    $post->setId($content[$i]->id);
                    $post->setSlug($content[$i]->slug);
                    $post->setDateTime($content[$i]->date);
                    $post->setCategories($content[$i]->categories);
                    $post->setTags($content[$i]->tags);
                    $post->setContent(htmlentities($content[$i]->content->rendered));
                    $post->setExcerpt(htmlentities($content[$i]->excerpt->rendered));
                    $post->setTitle(htmlentities($content[$i]->title->rendered));
                    $posts[$post->getId()] = $post;
                }
            } else {
                return false;
            }
        } while (count($content) == $this::ITEMS_PER_PAGE);
        ksort($posts);
        return $posts;
    }


    /**
     * @return Page[]|bool
     */
    public function getPages(): iterable | bool
    {
        $requestUri = $this->getWordpressUri() . "/wp-json/wp/v2/pages";
        $currentPage = 0;
        $pages = [];

        do {
            $currentPage++;
            try {
                $response = $this->requestFactory->request($requestUri . "?per_page=" . $this::ITEMS_PER_PAGE . "&_fields=id,slug,title,content,excerpt,parent&page=" . $currentPage,'GET');
            } catch (RequestException $e) {
                return false;
            }

            if ($response->getStatusCode() === 200) {
                $content = json_decode($response->getBody()->getContents());
                for ($i = 0; $i < count($content); $i++) {
                    $page = new Page();
                    $page->setId($content[$i]->id);
                    $page->setSlug($content[$i]->slug);
                    $page->setParent($content[$i]->parent);
                    $page->setContent(htmlentities($content[$i]->content->rendered));
                    $page->setExcerpt(htmlentities($content[$i]->excerpt->rendered));
                    $page->setTitle(htmlentities($content[$i]->title->rendered));

                    $pages[$page->getId()] = $page;
                }
            } else {
                return false;
            }
        } while (count($content) == $this::ITEMS_PER_PAGE);
        ksort($pages);
        return $pages;
    }

    /**
     * @return Comment[]|bool
     */
    public function getComments(): iterable | bool
    {
        $requestUri = $this->getWordpressUri() . "/wp-json/wp/v2/comments";
        $currentPage = 0;
        $comments = [];

        do {
            $currentPage++;
            try {
                $response = $this->requestFactory->request($requestUri . "?per_page=" . $this::ITEMS_PER_PAGE . "&_fields=id,post,parent,content&page=" . $currentPage,'GET');
            } catch (RequestException $e) {
                return false;
            }

            if ($response->getStatusCode() === 200) {
                $content = json_decode($response->getBody()->getContents());
                for ($i = 0; $i < count($content); $i++) {
                    $comment = new Comment();
                    $comment->setId($content[$i]->id);
                    $comment->setPost($content[$i]->post);
                    $comment->setParent($content[$i]->parent);
                    $comment->setContent(htmlentities($content[$i]->content->rendered));

                    $comments[$comment->getId()] = $comment;
                }
            } else {
                return false;
            }
        } while (count($content) == $this::ITEMS_PER_PAGE);
        ksort($comments);
        return $comments;
    }

    /**
     * @return Tag[]|bool
     */
    public function getTags(): iterable | bool
    {
        $requestUri = $this->getWordpressUri() . "/wp-json/wp/v2/tags";
        $currentPage = 0;
        $tags = [];

        do {
            $currentPage++;
            try {
                $response = $this->requestFactory->request($requestUri . "?per_page=" . $this::ITEMS_PER_PAGE . "&_fields=id,name,description,slug&page=" . $currentPage,'GET');
            } catch (RequestException $e) {
                return false;
            }

            if ($response->getStatusCode() === 200) {
                $content = json_decode($response->getBody()->getContents());
                for ($i = 0; $i < count($content); $i++) {
                    $tag = new Tag();
                    $tag->setId($content[$i]->id);
                    $tag->setDescription($content[$i]->description);
                    $tag->setName($content[$i]->name);
                    $tag->setSlug($content[$i]->slug);

                    $tags[$tag->getId()] = $tag;
                }
            } else {
                return false;
            }
        } while (count($content) == $this::ITEMS_PER_PAGE);
        ksort($tags);
        return $tags;
    }

    /**
     * @return Category[]|bool
     */
    public function getCategories(): iterable | bool
    {
        $requestUri = $this->getWordpressUri() . "/wp-json/wp/v2/categories";
        $currentPage = 0;
        $categories = [];

        do {
            $currentPage++;
            try {
                $response = $this->requestFactory->request($requestUri . "?per_page=" . $this::ITEMS_PER_PAGE . "&_fields=id,name,parent,slug&page=" . $currentPage,'GET');
            } catch (RequestException $e) {
                return false;
            }

            if ($response->getStatusCode() === 200) {
                $content = json_decode($response->getBody()->getContents());
                for ($i = 0; $i < count($content); $i++) {
                    $category = new Category();
                    $category->setId($content[$i]->id);
                    $category->setParent($content[$i]->parent);
                    $category->setName($content[$i]->name);
                    $category->setSlug($content[$i]->slug);

                    $categories[$category->getId()] = $category;
                }
            } else {
                return false;
            }
        } while (count($content) == $this::ITEMS_PER_PAGE);
        ksort($categories);
        return $categories;
    }
}