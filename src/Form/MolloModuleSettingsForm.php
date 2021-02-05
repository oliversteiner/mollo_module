<?php
/*
 * Introduction to Form API
 * https://www.drupal.org/docs/drupal-apis/form-api/introduction-to-form-api
 *
 * Form and render elements
 * https://api.drupal.org/api/drupal/elements/
 *
 * With Help from
 * https://www.drupal.org/project/gin_login
 *
 * Alternative: Generate Config Form
 * drush generate form-config
 *
 */

namespace Drupal\mollo_module\Form;

use Drupal\Core\File\Exception\FileException;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManager;

/**
 * Class SettingsForm.
 */
class MolloModuleSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'mollo_module.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mollo_module_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('mollo_module.settings');
    $default_scheme = $this->config('system.file')->get('default_scheme');

    // Checkboxes
    // ------------------------------------------------

    $form['section_checkbox'] = [
      '#type' => 'details',
      '#title' => t('Checkboxes'),
      '#open' => TRUE,
    ];
    $form['section_checkbox']['foo'] = [
      '#type' => 'checkbox',
      '#title' => t('Checkbox Foo'),
      '#default_value' => $config->get('section_checkbox.foo'),
      '#tree' => FALSE,
    ];
    $form['section_checkbox']['bar'] = [
      '#type' => 'checkbox',
      '#title' => t('Checkbox Bar'),
      '#default_value' => $config->get('section_checkbox.bar'),
      '#tree' => FALSE,
    ];

    // Textfield
    // ------------------------------------------------

    $form['section_text'] = [
      '#type' => 'details',
      '#title' => t('Text Field'),
      '#open' => TRUE,
    ];
    $form['section_text']['title'] = [
      '#type' => 'textfield',
      '#title' => t('Title'),
      '#default_value' => $config->get('section_text.title'),
    ];
    $form['section_text']['description'] = [
      '#type' => 'textfield',
      '#title' => t('Description'),
      '#default_value' => $config->get('section_text.description'),
    ];

    // Radio
    // ------------------------------------------------

    $form['section_radio'] = [
      '#type' => 'details',
      '#title' => t('Radio'),
      '#open' => TRUE,
    ];
    $form['section_radio']['active'] = [
      '#type' => 'radios',
      '#title' => $this
        ->t('Radio status'),
      '#default_value' => $config->get('section_radio.active'),
      '#options' => [
        0 => $this
          ->t('Closed'),
        1 => $this
          ->t('Active'),
      ],
    ];

    // Files
    // ------------------------------------------------

    $form['section_files'] = [
      '#type' => 'details',
      '#title' => t('Files'),
      '#open' => TRUE,
    ];
    $form['section_files']['image_path'] = [
      '#type' => 'textfield',
      '#title' => t('Path to image'),
      '#default_value' => $config->get('image_path.path') ? str_replace($default_scheme . '://', "", $config->get('image_path.path')) : '',
    ];
    $form['section_files']['image_upload'] = [
      '#type' => 'file',
      '#title' => t('Upload image'),
      '#maxlength' => 40,
      '#description' => t("If you don't have direct file access to the server, use this field to upload your image."),
      '#upload_validators' => [
        'file_validate_is_image' => [],
      ],
    ];


    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $moduleHandler = \Drupal::service('module_handler');

    if ($moduleHandler->moduleExists('file')) {
      // Check for a new uploaded file.
      if (isset($form['section_files'])) {
        $file = _file_save_upload_from_form($form['section_files']['image_upload'], $form_state, 0);
        if ($file) {
          // Put the temporary file in form_values so we can save it on submit.
          $form_state->setValue('image_upload', $file);
        }
      }

      // If the user provided a path for a logo or favicon file, make sure a file
      // exists at that path.
      if ($form_state->getValue('image_path')) {
        $path = $this->validatePath($form_state->getValue('image_path'));
        if (!$path) {
          $form_state->setErrorByName('image_path', $this->t('The custom image path is invalid.'));
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $values = $form_state->getValues();
    $file_system = \Drupal::service('file_system');
    $default_scheme = $this->config('system.file')->get('default_scheme');
    $config = $this->config('mollo_module.settings');
    try {
      if (!empty($values['image_upload'])) {
        $filename = $file_system->copy($values['image_upload']->getFileUri(), $default_scheme . '://');
        $values['image_path'] = $filename;
      }
    } catch (FileException $e) {
      // Ignore.
    }

    unset($values['image_upload']);

    // If the user entered a path relative to the system files directory for
    // a logo store a public:// URI so the theme system can handle it.
    if (!empty($values['image_path'])) {
      $values['image_path'] = $this->validatePath($values['image_path']);
    }

    /**
     *
     *  section_checkbox
     *  - foo
     *  - bar
     *
     *  section_radio
     *  - active
     *
     *  section_text
     *  - title
     *  - description
     *
     *  section_file
     *  - image_path
     */

    // dd($values);

    foreach ($values as $key => $value) {

      // checkbox
      if ($key === 'foo') {
        $config->set('section_checkbox.foo', $value);
      }
      elseif ($key === 'bar') {
        $config->set('section_checkbox.bar', $value);
      }
      // Radio
      elseif ($key === 'active') {
        $config->set('section_radio.active', $value);
      }
      // Text
      elseif ($key === 'title') {
        $config->set('section_text.title', $value);
      }
      elseif ($key === 'description') {
        $config->set('section_text.description', $value);
      }
      // files
      elseif ($key === 'image_path') {
        $config->set('section_file.image_path', $value);
      }
    }

    $config->save();
    // Rebuild the router.
    \Drupal::service('router.builder')->rebuild();
  }

  /**
   * Helper function for the system_theme_settings form.
   *
   * Attempt to validate normal system paths, paths relative to the public files
   * directory, or stream wrapper URIs. If the given path is any of the above,
   * returns a valid path or URI that the theme system can display.
   *
   * @param string $path
   *   A path relative to the Drupal root or to the public files directory, or
   *   a stream wrapper URI.
   *
   * @return mixed
   *   A valid path that can be displayed through the theme system, or FALSE if
   *   the path could not be validated.
   */
  protected function validatePath($path) {
    $file_system = \Drupal::service('file_system');
    // Absolute local file paths are invalid.
    if ($file_system->realpath($path) == $path) {
      return FALSE;
    }
    // A path relative to the Drupal root or a fully qualified URI is valid.
    if (is_file($path)) {
      return $path;
    }
    // Prepend 'public://' for relative file paths within public filesystem.
    if (StreamWrapperManager::getScheme($path) === FALSE) {
      $path = 'public://' . $path;
    }
    if (is_file($path)) {
      return $path;
    }
    return FALSE;
  }

}
