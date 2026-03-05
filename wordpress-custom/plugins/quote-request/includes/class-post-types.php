<?php
/**
 * Custom Post Types
 *
 * Registreert en beheert eigen content types,
 * vergelijkbaar met WordPress register_post_type()
 */

class PostTypes
{
    private Database $db;

    /** @var array<string, array> Geregistreerde post types */
    private array $registeredTypes = [];

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Initialiseer: registreer de custom post types via hooks
     * (vergelijkbaar met WordPress 'init' hook)
     */
    public function register(): void
    {
        add_action('init', [$this, 'registerPostTypes']);
    }

    /**
     * Registreert alle custom post types
     * Wordt uitgevoerd op de 'init' hook
     */
    public function registerPostTypes(): void
    {
        $this->registerPostType('project', [
            'label'       => 'Projecten',
            'label_single'=> 'Project',
            'description' => 'Portfolio projecten',
            'public'      => true,
            'has_archive' => true,
            'supports'    => ['title', 'content', 'excerpt'],
        ]);

        $this->registerPostType('service', [
            'label'       => 'Diensten',
            'label_single'=> 'Dienst',
            'description' => 'Aangeboden diensten',
            'public'      => true,
            'has_archive' => true,
            'supports'    => ['title', 'content'],
        ]);
    }

    /**
     * Registreert een enkel post type (vergelijkbaar met register_post_type())
     */
    public function registerPostType(string $type, array $args): void
    {
        $defaults = [
            'label'       => ucfirst($type),
            'label_single'=> ucfirst($type),
            'description' => '',
            'public'      => true,
            'has_archive' => false,
            'supports'    => ['title', 'content'],
        ];

        $this->registeredTypes[$type] = array_merge($defaults, $args);

        // Trigger action zodat andere plugins kunnen reageren
        do_action('registered_post_type', $type, $this->registeredTypes[$type]);
    }

    /**
     * Haal posts op voor een bepaald type
     *
     * @param string $postType  Het post type
     * @param string $status    Post status (publish/draft/trash)
     * @param int    $limit     Max aantal resultaten
     */
    public function getPosts(string $postType, string $status = 'publish', int $limit = 10): array
    {
        $sql = 'SELECT * FROM posts WHERE post_type = ? AND status = ? ORDER BY created_at DESC LIMIT ?';
        $posts = $this->db->query($sql, [$postType, $status, $limit]);

        // Voeg meta data toe aan elk post
        foreach ($posts as &$post) {
            $post['meta'] = $this->getPostMeta($post['id']);
        }

        // Filter: geeft andere code de kans posts te modificeren
        return apply_filters('the_posts', $posts, $postType);
    }

    /**
     * Haal één post op via ID
     */
    public function getPost(int $id): ?array
    {
        $post = $this->db->queryOne('SELECT * FROM posts WHERE id = ?', [$id]);
        if ($post) {
            $post['meta'] = $this->getPostMeta($id);
        }
        return $post;
    }

    /**
     * Haal alle meta data op voor een post
     */
    public function getPostMeta(int $postId): array
    {
        $rows = $this->db->query(
            'SELECT meta_key, meta_value FROM post_meta WHERE post_id = ?',
            [$postId]
        );

        $meta = [];
        foreach ($rows as $row) {
            $meta[$row['meta_key']] = $row['meta_value'];
        }
        return $meta;
    }

    /**
     * Sla een nieuwe post op
     */
    public function insertPost(array $data): int
    {
        $data['slug'] = $data['slug'] ?? sanitize_slug($data['title']);

        $postId = $this->db->insert('posts', [
            'post_type' => $data['post_type'] ?? 'post',
            'title'     => sanitize_text($data['title']),
            'content'   => $data['content'] ?? '',
            'excerpt'   => $data['excerpt'] ?? '',
            'status'    => $data['status'] ?? 'draft',
            'author'    => $data['author'] ?? 'Admin',
            'slug'      => $data['slug'],
        ]);

        // Meta data opslaan
        if (!empty($data['meta']) && is_array($data['meta'])) {
            foreach ($data['meta'] as $key => $value) {
                $this->addPostMeta($postId, $key, $value);
            }
        }

        do_action('save_post', $postId, $data);

        return $postId;
    }

    /**
     * Voeg meta data toe aan een post
     */
    public function addPostMeta(int $postId, string $key, string $value): void
    {
        $this->db->insert('post_meta', [
            'post_id'    => $postId,
            'meta_key'   => $key,
            'meta_value' => $value,
        ]);
    }

    /**
     * Geeft alle geregistreerde post types terug
     */
    public function getRegisteredTypes(): array
    {
        return $this->registeredTypes;
    }
}
