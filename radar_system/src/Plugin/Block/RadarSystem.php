<?php
namespace Drupal\radar_system\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Radar System' block.
 *
 * @Block(
 *   id = "radar_system_Block",
 *   admin_label = @Translation("Radar System")
 * )
 */
class RadarSystem extends BlockBase implements ContainerFactoryPluginInterface
{

    /**
     * @var service variable
     */
    protected $radarsystemService;

    /**
     *  @param array $configuration
     *  @param string $plugin_id
     *  @param mixed $plugin_definition
     *  @param \Drupal\radar_system\RadarSystemService $radarsystemService
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, $radarsystemService)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->radarsystemService = $radarsystemService;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static ($configuration, $plugin_id, $plugin_definition, $container->get('radar.system.service'));
    }

    /**
     * {@inheritdoc}
     */
    protected function blockAccess(AccountInterface $account)
    {
        return AccessResult::allowedIfHasPermission($account, 'access content');
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $radarDetails = $this
            ->radarsystemService
            ->getRadar();
        $country = $city = $time = '';
        if (!empty($radarDetails))
        {
            $country = $radarDetails['country'];
            $city = $radarDetails['city'];
            $time = $radarDetails['time'];
        }
        return ['#theme' => 'radarsystem', '#radar_country' => $country, '#radar_city' => $city, '#radar_time' => $time, '#cache' => ['max-age' => $radarDetails['totalSeconds']], ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheTags()
    {
        return Cache::mergeTags(parent::getCacheTags() , array(
            'radarsystemtags'
        ));
    }
}