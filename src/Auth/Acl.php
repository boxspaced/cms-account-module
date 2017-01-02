<?php
namespace Account\Auth;

use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Account\Controller\AccountController;
use Block\Controller\BlockController;
use Item\Controller\ItemController;
use Form\Controller\FormController;
use Menu\Controller\MenuController;
use Standalone\Controller\StandaloneController;
use Workflow\Controller\WorkflowController;
use DigitalGallery\Controller\DigitalGalleryController;
use Course\Controller\CourseController;
use WhatsOn\Controller\WhatsOnController;
use Search\Controller\SearchController;
use Asset\Controller\AssetController;
use Helpdesk\Controller\HelpdeskController;

class Acl extends ZendAcl
{

    public function __construct()
    {
        // Resources
        $this->addResource(new Resource(AccountController::class));
        $this->addResource(new Resource(BlockController::class));
        $this->addResource(new Resource(ItemController::class));
        $this->addResource(new Resource(FormController::class));
        $this->addResource(new Resource(MenuController::class));
        $this->addResource(new Resource(StandaloneController::class));
        $this->addResource(new Resource(WorkflowController::class));
        $this->addResource(new Resource(DigitalGalleryController::class));
        $this->addResource(new Resource(CourseController::class));
        $this->addResource(new Resource(WhatsOnController::class));
        $this->addResource(new Resource(SearchController::class));
        $this->addResource(new Resource(AssetController::class));
        $this->addResource(new Resource(HelpdeskController::class));

        // Roles
        $this->addRole(new Role('guest'));
        $this->addRole(new Role('author'), 'guest');
        $this->addRole(new Role('publisher'), 'author');
        $this->addRole(new Role('digital-gallery-manager'), 'guest');
        $this->addRole(new Role('course-manager'), 'guest');
        $this->addRole(new Role('whats-on-manager'), 'guest');
        $this->addRole(new Role('asset-manager'), 'guest');
        $this->addRole(new Role('form-manager'), 'guest');
        $this->addRole(new Role('helpdesk-user'), 'guest');
        $this->addRole(new Role('helpdesk-manager'), 'helpdesk-user');
        $this->addRole(new Role('admin'), array(
            'publisher',
            'digital-gallery-manager',
            'course-manager',
            'whats-on-manager',
            'asset-manager',
            'form-manager',
            'helpdesk-manager',
        ));

        // Rules
        $this->allow('guest', AccountController::class, array('login', 'logout', 'access-denied'));
        $this->allow('guest', ItemController::class, array('view'));
        $this->allow('guest', DigitalGalleryController::class, array('search', 'results', 'view', 'add-to-basket', 'empty-basket', 'basket', 'download'));
        $this->allow('guest', CourseController::class, array('search', 'results', 'view'));
        $this->allow('guest', WhatsOnController::class, array('search', 'results', 'view'));
        $this->allow('guest', SearchController::class, array('simple'));
        $this->allow('guest', FormController::class, array(
            'contact-us',
            'learning-enquiry',
            'it-contact',
            'stock-suggestion',
            'volunteer-application',
            'online-evaluation-for-partners',
            'school-work-experience-application',
            'placement-application',
            'teens-music-trivia-competition',
            'thank-you',
        ));

        $this->allow('author', AccountController::class, array('index'));
        $this->allow('author', BlockController::class, array('create', 'edit', 'index'));
        $this->allow('author', ItemController::class, array('create', 'edit'));
        $this->allow('author', MenuController::class, array('index', 'internal-links'));
        $this->allow('author', WorkflowController::class, array('authoring', 'authoring-delete'));
        $this->allow('author', StandaloneController::class, array('index'));

        $this->allow('publisher', BlockController::class, array('publish', 'delete', 'publish-update'));
        $this->allow('publisher', ItemController::class, array('publish', 'delete', 'publish-update'));
        $this->allow('publisher', WorkflowController::class, array('publishing', 'send-back', 'publishing-delete'));
        $this->allow('publisher', MenuController::class, array('shuffle'));

        $this->allow('digital-gallery-manager', AccountController::class, array('index'));
        $this->allow('digital-gallery-manager', DigitalGalleryController::class, array('manage', 'upload', 'edit', 'delete', 'categories',
            'create-category', 'edit-category', 'delete-category',
            'publish', 'import', 'reindex'));

        $this->allow('course-manager', AccountController::class, array('index'));
        $this->allow('course-manager', CourseController::class, array('manage', 'publish', 'import', 'reindex'));

        $this->allow('whats-on-manager', AccountController::class, array('index'));
        $this->allow('whats-on-manager', WhatsOnController::class, array('manage', 'publish', 'import', 'reindex'));

        $this->allow('asset-manager', AccountController::class, array('index'));
        $this->allow('asset-manager', AssetController::class);

        $this->allow('form-manager', AccountController::class, array('index'));
        $this->allow('form-manager', FormController::class, array('manage', 'publish'));

        $this->allow('helpdesk-user', AccountController::class, array('index'));
        $this->allow('helpdesk-user', HelpdeskController::class, array('index', 'create-ticket', 'view-ticket', 'view-attachment'));
        $this->allow('helpdesk-manager', HelpdeskController::class, array('resolve-ticket'));
    }

    /**
     * @param string[] $roles
     * @param string $resource
     * @param string $action
     * @return bool
     */
    public function isAllowedMultiRoles(array $roles, $resource, $action)
    {
        foreach ($roles as $role) {

            if ($this->isAllowed($role, $resource, $action)) {
                return true;
            }
        }

        return false;
    }

}
