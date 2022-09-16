<?php
namespace Drupal\radar_system\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Language settings for this site.
 */

class RadarSettingsForm extends ConfigFormBase
{

    /** @var string Config settings */
    const SETTINGS = 'radar.settings';

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'radar_admin_settings';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [static ::SETTINGS, ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {

        $config = $this->config(static ::SETTINGS);

        $form['country'] = ['#type' => 'textfield', '#title' => $this->t('Country') , '#default_value' => $config->get('country') , ];

        $form['city'] = ['#type' => 'textfield', '#title' => $this->t('City') , '#default_value' => $config->get('city') , ];

        $form['timezone'] = ['#type' => 'select', '#title' => $this->t('Timezone') , '#options' => ['America/Chicago' => 'America/Chicago', 'America/New_York' => 'America/New_York', 'Asia/Tokyo' => 'Asia/Tokyo', 'Asia/Dubai' => 'Asia/Dubai', 'Asia/Kolkata' => 'Asia/Kolkata', 'Europe/Amsterdam' => 'Europe/Amsterdam', 'Europe/Oslo' => 'Europe/Oslo', 'Europe/London' => 'Europe/London', ], '#default_value' => $config->get('timezone') , ];

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array & $form, FormStateInterface $form_state)
    {

        Cache::invalidateTags(['radar_system_tag', 'radarsystemtags']);

        $conf = $this
            ->configFactory
            ->getEditable(static ::SETTINGS);
        $conf->set('country', $form_state->getValue('country'));
        $conf->set('city', $form_state->getValue('city'));
        $conf->set('timezone', $form_state->getValue('timezone'));
        $conf->save();

        parent::submitForm($form, $form_state);
    }
}