<?php


class GetterSetter {
    public $settings;

	public function __call($methodName, $params = null){
		$methodPrefix = substr($methodName, 0, 3);
        $key = substr($methodName, 3);
        if($methodPrefix == 'set' && count($params) == 1)
        {
            $value = $params[0];
            $this->settings[$key] = $value;
            return $this;
        }
        elseif($methodPrefix == 'get')
        {
            if(is_array($this->settings))
                if(array_key_exists($key, $this->settings)) return $this->settings[$key];
        }
        else
        {
            exit("O método $methodName nao definido!");
        }
	}

    public function setData(array $data){
        foreach ($data as $row) {
            foreach ($row as $key => $value) {
                $this->{'set'.$key}($value);
            }
        }

    }

}
