<?php

namespace Doornock\Model\DoorModule;

use Nette\Http\Url;
use Symfony\Component\Yaml\Yaml;

class YamlGenerator implements ConfigurationGenerator
{

	/** @var NodeRepository */
	private $nodeRepository;

	/** @var SiteInformation */
	private $siteInformation;

	/** @var string */
	private $apiUrl;

	/**
	 * YamlGenerator constructor.
	 * @param NodeRepository $nodeRepository
	 * @param SiteInformation $siteInformation
	 */
	public function __construct(NodeRepository $nodeRepository, SiteInformation $siteInformation)
	{
		$this->nodeRepository = $nodeRepository;
		$this->siteInformation = $siteInformation;
	}


	/**
	 * @param string $apiUrl
	 */
	public function setApiUrl($apiUrl)
	{
		$this->apiUrl = $apiUrl;
	}

	/**
	 * Generate YAML configuration
	 * @param Node $node
	 * @return string
	 */
	public function generate(Node $node)
	{
		$config = array(
			'site' => array(
				'guid' => $this->siteInformation->getGuid(),
			),
			'nfc' => array(
				'aid' => 'F0394148148111'
			)
		);

		if ($node->getApiEndpointUrl()) {
			$url = new Url($node->getApiEndpointUrl());
			$config['httpApi'] = array(
				'url' => $this->apiUrl ?: "# write here url server here server like http://mylock.home.com/api",
				'nodeId' => (string) $node->getId(),
				'apiKey' => $node->getApiKey(),
				'port' => $url->getPort()
			);
		}

		$doors = array();
		foreach ($node->getDoors() as $door) { /** @var $door Door */
			$doors[] = array(
				'id' => (string) $door->getId(),
				'type' => 'gpio',
				'gpio' => '# write gpio port by wiring pi!'
			);
		}
		$config['doors'] = $doors;

		return Yaml::dump($config, 3); # 3 = depth of not inline dump
	}


}