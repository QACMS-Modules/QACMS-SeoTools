<?php 
class ToolsController extends SeoToolsAppController {
    public $components = array('SeoTools.SeoTools');
    public $uses = array('System.Module');

    public function beforeFilter(){
        parent::beforeFilter();

        if(!empty($this->data) && $this->action == 'admin_execute') {
            $this->QuickApps->disableSecurity();
        }
    }

    public function admin_index() {
        $this->Layout['stylesheets']['all'][] = '/seo_tools/css/styles.css';

        $this->set('tools', $this->SeoTools->toolsList());
        $this->setCrumb(
            '/admin/seo_tools',
            array(__d('seo_tools', 'Tools'))
        );
    }

    public function admin_execute($tool) {
		$this->Layout['stylesheets']['all'][] = '/seo_tools/css/styles.css';
        $Tool = $this->SeoTools->loadTool($tool);

        if (!empty($this->data)) {
            if ($execute = $Tool->main($this)) {
				if (!$this->Module->getDataSource()->isConnected()) {
					$this->Module->getDataSource()->reconnect();
				}

                $this->set('results', $execute);
            } else {
                $this->redirect("/admin/seo_tools/tools/execute/{$tool}");
            }
        } else {
            $data['Tool']['url'] = QuickApps::strip_language_prefix(Router::url('/', true));
            $this->data = $data;
			$this->set('parseUrl', $this->SeoTools->parseUrl(QuickApps::strip_language_prefix(Router::url('/', true))));
        }

        $tool_info = $this->SeoTools->toolInfo($tool);

        $this->set('tool', $tool);
        $this->set('tool_info', $tool_info);
        $this->setCrumb(
            '/admin/seo_tools',
            array(__d('seo_tools', 'Tools'), '/admin/seo_tools/tools/'),
            array($tool_info['name'])
        );
    } 
}