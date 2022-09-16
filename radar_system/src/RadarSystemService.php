<?php
namespace Drupal\radar_system;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Cache\Cache;

/**
 * Defines an importer of radar items.
 */
class RadarSystemService
{

    /**
     * The radar.settings config object.
     *
     * @var \Drupal\Core\Config\Config
     */
    protected $config;

    /**
     * Constructs an Importer object.
     *
     * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
     *   The factory for configuration objects.
     */
    public function __construct(ConfigFactoryInterface $configFactory)
    {
        $this->config = $configFactory->get('radar.settings');
    }

    /**
     * {@inheritdoc}
     */
    public function getRadar()
    {
        $cache = \Drupal::cache();
        $cacheData = $cache->get('radar_system');
        if (empty($cacheData))
        {
            $radar = [];
            if (!empty($this
                ->config
                ->get('country')))
            {
                $radar['country'] = $this
                    ->config
                    ->get('country');
            }
            if (!empty($this
                ->config
                ->get('city')))
            {
                $radar['city'] = $this
                    ->config
                    ->get('city');
            }
            if (!empty($this
                ->config
                ->get('timezone')))
            {
                $date = new DrupalDateTime();
                $date->setTimezone(new \DateTimeZone($this
                    ->config
                    ->get('timezone')));
                $radar['time'] = $date->format('jS M Y - g:i A');
                $seconds = $date->format('s');
                $radar['totalSeconds'] = 60 - $seconds;
            }
            $cache->set('radar_system', $radar, \Drupal::time()->getRequestTime() + $radar['totalSeconds'], ['radar_system_tag']);
            return $radar;
        }
        else
        {
            return $cacheData->data;
        }
    }

}