<?php
class ControllerExtensionModuleGallery extends Controller {
	public function index($setting) {
		static $module = 0;

		if (empty($setting['gallery'])) {
			return;
		}

		$this->document->addStyle('catalog/view/theme/default/stylesheet/gallery.css');

		$data['width'] = $setting['width'];

		$this->load->model('tool/image');

		$data['images'] = array();

		foreach ($setting['gallery'] as $gallery) {
			$image_path = DIR_IMAGE . $gallery['image'];

			if (is_file($image_path)) {
				$size = getimagesize($image_path);

				if ($this->request->server['HTTPS']) {
					$image_url = HTTPS_SERVER . 'image/' . $gallery['image'];
				} else {
					$image_url =  HTTP_SERVER . 'image/' . $gallery['image'];
				}

				$ratio = $size[0] / $size[1];

				$height = $setting['width'] / $ratio;

				$data['images'][] = array(
					'title'  => trim(html_entity_decode($gallery['description'][$this->config->get('config_language_id')]['title'], ENT_QUOTES, 'UTF-8')),
					'link'  => trim(html_entity_decode($gallery['description'][$this->config->get('config_language_id')]['link'], ENT_QUOTES, 'UTF-8')),
					'popup' => $image_url,
					'thumb' => $this->model_tool_image->resize($gallery['image'], $setting['width'], $height),
					'sort' => $gallery['sort'],
				);
			}
		}

		// Sort array by 'sort' value
		usort($data['images'], function($a, $b) {
			return $a['sort'] - $b['sort'];
		});

		$data['module'] = $module++;

		if ($data['images']) {
			return $this->load->view('extension/module/gallery', $data);
		}
	}
}
