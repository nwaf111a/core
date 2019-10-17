<?php
/**
 *  Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) 2017-2019 Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license.html
 * 
*/
namespace Arikaim\Core\Traits\Db;

use Arikaim\Core\View\Template\Template;
use Arikaim\Core\Db\Model;

/**
 *  Translations trait      
*/
trait Translations 
{           
    /**
     * Get translation refernce attribute name 
     *
     * @return string|null
     */
    public function getTranslationReferenceAttributeName()
    {
        return (isset($this->translation_reference_attribute) == true) ? $this->translation_reference_attribute : null;
    }

    /**
     * Get translation miodel class
     *
     * @return string|null
     */
    public function getTranslationModelClass()
    {
        return (isset($this->translation_model_class) == true) ? $this->translation_model_class : null;
    }

    /**
     * HasMany relation
     *
     * @return void
     */
    public function translations()
    {
        $translation_model_class = $this->getTranslationModelClass();
        return $this->hasMany($translation_model_class);
    }

    /**
     * Get translations query
     *
     * @param string|mull $language
     * @return Builder
     */
    public function getTranslationsQuery($language = null)
    {
        $class = $this->getTranslationModelClass();
        $model = new $class();
        $language = (empty($language) == true) ? Template::getLanguage() : $language;
        
        return $model->where('language','=',$language);
    }

    /**
     * Get translation model
     *
     * @param string $language
     * @return Model|false
     */
    public function translation($language = null, $query = false)
    {
        $language = (empty($language) == true) ? Template::getLanguage() : $language;
        $model = $this->translations()->getQuery()->where('language','=',$language);
        $model = ($query == false) ? $model->first() : $model;

        return (is_object($model) == false) ? false : $model;
    }

    /**
     * Create or update translation 
     *
     * @param string|integer|null $id
     * @param array $data
     * @param string $language
     * @return Model
     */
    public function saveTranslation(array $data, $language = null, $id = null)
    {
        $language = (empty($language) == true) ? Template::getLanguage() : $language;
        $model = (empty($id) == true) ? $this : $this->findById($id);     
        $reference = $this->getTranslationReferenceAttributeName();

        $data['language'] = $language;
        $data[$reference] = $model->id;

        $translation = $model->translation($language);
    
        if ($translation === false) {
            return $model->translations()->create($data);
        } 
        $translation->update($data);  
        
        return $translation;
    }

    /**
     * Delete translation
     *
     * @param string|integer|null $id
     * @param string $language
     * @return boolean
     */
    public function removeTranslation($id = null, $language = null)
    {
        $language = (empty($language) == true) ? Template::getLanguage() : $language;
        $model = (empty($id) == true) ? $this : $this->findById($id);     
        $model = $model->translation($language);

        return (is_object($model) == true) ? $model->delete() : false;
    }

    /**
     * Delete all translations
     *
     * @param string|integer|null $id
     * @return boolean
     */
    public function removeTranslations($id = null)
    {
        $model = (empty($id) == true) ? $this : $this->findById($id);
        $model = $model->translations();

        return (is_object($model) == true) ? $model->delete() : false;
    }

    /**
     * Find Translation
     *
     * @param string $attribute_name
     * @param mixed $value
     * @return void
     */
    public function findTranslation($attribute_name, $value)
    {     
        $class = $this->getTranslationModelClass();
        $model = new $class();

        $model = $model->whereIgnoreCase($attribute_name,trim($value));
        return $model->first();
    }
}
