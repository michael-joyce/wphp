<?php

namespace AppBundle\Menu;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Nines\BlogBundle\Entity\PostCategory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Menu builder for the navigation and search menus.
 */
class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    // U+25BE, black down-pointing small triangle.
    const CARET = ' ▾';

    private $spotlightMenuItems;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Build the menu builder.
     */
    public function __construct($spotlightMenuItems, EntityManagerInterface $em, FactoryInterface $factory, AuthorizationCheckerInterface $authChecker, TokenStorageInterface $tokenStorage)
    {
        $this->spotlightMenuItems = $spotlightMenuItems;
        $this->em = $em;
        $this->factory = $factory;
        $this->authChecker = $authChecker;
        $this->tokenStorage = $tokenStorage;
    }

    private function hasRole($role)
    {
        if (!$this->tokenStorage->getToken()) {
            return false;
        }
        return $this->authChecker->isGranted($role);
    }

    /**
     * Build the navigation menu and return it.
     *
     * @param FactoryInterface $factory
     * @param array $options
     * @return ItemInterface
     */
    public function mainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(array(
            'class' => 'nav navbar-nav',
        ));

        $browse = $menu->addChild('browse', array(
            'uri' => '#',
            'label' => 'Explore ' . self::CARET,
        ));
        $browse->setAttribute('dropdown', true);
        $browse->setLinkAttribute('class', 'dropdown-toggle');
        $browse->setLinkAttribute('data-toggle', 'dropdown');
        $browse->setChildrenAttribute('class', 'dropdown-menu');

        $browse->addChild('Titles', array(
            'route' => 'title_index',
        ));
        $browse->addChild('Persons', array(
            'route' => 'person_index',
        ));
        $browse->addChild('Firms', array(
            'route' => 'firm_index',
        ));

        $browse->addChild('Formats', array(
            'route' => 'format_index',
        ));
        $browse->addChild('Genres', array(
            'route' => 'genre_index',
        ));
        $browse->addChild('Sources', array(
            'route' => 'source_index',
        ));
        $browse->addChild('Contributor Roles', array(
            'route' => 'role_index',
        ));
        $browse->addChild('Firm Roles', array(
            'route' => 'firmrole_index',
        ));

        if ($this->hasRole('ROLE_USER')) {
            $divider = $browse->addChild('divider', array(
                'label' => '',
            ));
            $divider->setAttributes(array(
                'role' => 'separator',
                'class' => 'divider',
            ));
            $browse->addChild('English Novel', array(
                'route' => 'resource_en_index',
            ));
            $browse->addChild('ESTC', array(
                'route' => 'resource_estc_index',
            ));
            $browse->addChild('Jackson', array(
                'route' => 'resource_jackson_index',
            ));
            $browse->addChild('Orlando', array(
                'route' => 'resource_orlando_biblio_index',
            ));
            $browse->addChild('Osborne', array(
                'route' => 'resource_osborne_index',
            ));
        }

        return $menu;
    }

    /**
     * Build the search menu and return it.
     *
     * @param FactoryInterface $factory
     * @param array $options
     * @return ItemInterface
     */
    public function searchMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(array(
            'class' => 'nav navbar-nav',
        ));

        $search = $menu->addChild('search', array(
            'uri' => '#',
            'label' => 'Search ' . self::CARET,
        ));
        $search->setAttribute('dropdown', true);
        $search->setLinkAttribute('class', 'dropdown-toggle');
        $search->setLinkAttribute('data-toggle', 'dropdown');
        $search->setChildrenAttribute('class', 'dropdown-menu');

        $search->addChild('Titles', array(
            'route' => 'title_search',
        ));
        $search->addChild('Persons', array(
            'route' => 'person_search',
        ));
        $search->addChild('Firms', array(
            'route' => 'firm_search',
        ));
        if ($this->hasRole('ROLE_USER')) {
            $divider = $search->addChild('divider', array(
                'label' => '',
            ));
            $divider->setAttributes(array(
                'role' => 'separator',
                'class' => 'divider',
            ));

            $search->addChild('Admin Reports', array(
                'uri' => '#',
            ));

            $search->addChild('First Pub Dates', array(
                'route' => 'report_first_pub_date',
            ));

            $search->addChild('Titles to Check', array(
                'route' => 'report_titles_check',
            ));
            $search->addChild('Firms to Check', array(
                'route' => 'report_firms_check',
            ));
            $search->addChild('Persons to Check', array(
                'route' => 'report_person_check',
            ));
            $search->addChild('Titles with missing Sources', array(
                'route' => 'report_title_source_id_null'
            ));
            $search->addChild('Titles with Missing Source IDs', array(
                'route' => 'report_title_source_null'
            ));
            $search->addChild('Titles without Genre', array(
                'route' => 'report_title_without_genre'
            ));
        }
        return $menu;
    }

    /**
     * Build the spotlight menu and return it.
     *
     * @param FactoryInterface $factory
     * @param array $options
     * @return ItemInterface
     */
    public function spotlightMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(array(
            'class' => 'nav navbar-nav',
        ));

        $spotlight = $menu->addChild('spotlight', array(
            'uri' => '#',
            'label' => 'Spotlight ' . self::CARET,
        ));
        $spotlight->setAttribute('dropdown', true);
        $spotlight->setLinkAttribute('class', 'dropdown-toggle');
        $spotlight->setLinkAttribute('data-toggle', 'dropdown');
        $spotlight->setChildrenAttribute('class', 'dropdown-menu');

        $repo = $this->em->getRepository(PostCategory::class);
        foreach ($this->spotlightMenuItems as $item) {
            $category = $repo->findOneBy(array(
                'name' => $item,
            ));
            if (!$category) {
                continue;
            }
            $spotlight->addChild($category->getLabel(), array(
                'route' => 'post_category_show',
                'routeParameters' => array(
                    'id' => $category->getId(),
                )
            ));
        }
        return $menu;
    }
}
