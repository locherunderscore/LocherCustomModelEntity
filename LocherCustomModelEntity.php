<?php

namespace LocherCustomModelEntity;

use LocherCustomModelEntity\Models\LocherCustomModel;
use Doctrine\ORM\Tools\SchemaTool;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Models\Site\Site;

class LocherCustomModelEntity extends Plugin {

    private $filteredExistingModel = "LocherCustomModelEntity\LocherCustomModel";

    public static function getSubscribedEvents() {
        return [
            'Shopware_Controllers_Backend_Attributes::getEntitiesAction::after' => 'addCustomEntities',
            'Shopware_Controllers_Backend_EntitySearch::searchAction::before' => 'displayCustomModelEntries',
        ];
    }

    /* ---SUBSCRIBER FUNCTIONS--- */
    public function addCustomEntities(\Enlight_Hook_HookArgs $args){
        $assignedData = $args->getSubject()->View()->getAssign();
        $assignedData['data'][] = ['entity' => LocherCustomModel::class, 'label' => "LocherCustomModel"];
        $assignedData['data'][] = ['entity' => $this->filteredExistingModel, 'label' => "LocherFilteredModel"];
        $args->getSubject()->View()->assign($assignedData);
    }
    public function displayCustomModelEntries(\Enlight_Hook_HookArgs $args) {
        $this->displayCustomModel();
        $this->displayFilteredModel($args);
    }

    /* ---HELPER FUNCTIONS--- */
    private function createNewSchema() {
        $entityManager = Shopware()->Container()->get('models');
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema([$entityManager->getClassMetadata(LocherCustomModel::class)], true);
    }
    private function displayCustomModel() {
        $entityManager = Shopware()->Container()->get('models');
        $new_bottom = Shopware()->Container()->get('models')->getRepository(Site::class)->findBy(['grouping' => 'bottom']);
        $current_entity = Shopware()->Container()->get('models')->getRepository(LocherCustomModel::class)->findAll();
        foreach ($new_bottom as $new_entry) {
            $exist = false;
            if (count($current_entity) > 0) {
                foreach ((array) $current_entity as $current_sizeChart) {
                    if ($new_entry->getId() == $current_sizeChart->getCMSId()) {
                        $exist = true;
                        break;
                    }
                }
            }
            if (!$exist) {
                //We maintain our own list of database-entries for our custom model
                $newModel = new LocherCustomModel();
                $newModel->setCMSId($new_entry->getId());
                $newModel->setName($new_entry->getDescription());
                $newModel->setHtml($new_entry->getHtml());
                $entityManager->persist($newModel);
                $entityManager->flush($newModel);
            }
        }
    }
    private function displayFilteredModel(\Enlight_Hook_HookArgs $args) {
        $request = $args->getSubject()->Request();
        if ($request->getParam('model') == $this->filteredExistingModel) {
            $customFilter = array(array('property' => "grouping", 'value' => "bottom")); // We add a custom filter over an existing shopware-model
            $request->setParam('filter', $customFilter);
            $request->setParam('model', Site::class);
        }

    }

    /* ---PLUGIN MANAGEMENT--- */
    public function install(InstallContext $context) {
        $this->createNewSchema();
        $context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
    }
    public function update(UpdateContext $context) {
        return true;
    }
    public function uninstall(UninstallContext $context) {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
    }
}