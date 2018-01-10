<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_base_v2 extends CI_Model
{
    protected $model_path = '';
    protected $model_suffix = '_model';
    protected $entity_suffix = '_entity';
    protected $class_name;
    protected $model_name;
    protected $table_name;
    protected $entity_name;
    protected $call_properties = array();

    /**
     * Model_base constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_init_names();
        $this->_load_entity();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }

        /**
         * CI_Model
         */
        return parent::__get($name);
    }

    private function _where_filter_site_seq($where)
    {
        if (is_array($where)) {
            $where['site_seq'] = SITE_SEQ;
        }
        if (is_string($where)) {
            $where .= ' AND site_seq = ' . SITE_SEQ;
        }

        return $where;
    }

    /**
     * 클래스명, 테이블명 초기화
     */
    private function _init_names()
    {
        $this->class_name = get_class($this);
        $this->model_name = $this->class_name;
        $this->table_name = strtoupper($this->class_name);
        if (strpos($this->class_name, $this->model_suffix) !== false) {
            $this->table_name = substr($this->class_name, 0, strpos($this->class_name, $this->model_suffix));
            $this->table_name = strtoupper($this->table_name);
        }

        // entity_name
        $this->entity_name = ucfirst(strtolower($this->table_name)) . $this->entity_suffix;
    }

    private function _load_entity()
    {
        $this->load->model("{$this->model_path}/{$this->entity_name}", $this->entity_name);
    }

    /**
     * @param array $where
     * @return object|Entity_base
     */
    public function get_one($where = array())
    {
        $where = $this->_where_filter_site_seq($where);
        return $this->db
            ->where($where)
            ->get($this->table_name, 1)
            ->custom_row_object(0, $this->entity_name);
    }

    /**
     * @param array $where
     * @return array
     */
    public function get_many($where = array())
    {
        $where = $this->_where_filter_site_seq($where);

        return $this->db
            ->where($where)
            ->get($this->table_name)
            ->custom_result_object($this->entity_name);
    }

    /**
     * 주의: entity 내용을 전부 포함하게 되므로 출력 용도로는 부적합함
     *
     * @param array|string $properties
     * @return $this
     */
    public function with_property($properties)
    {
        if (is_string($properties))
            $properties = array($properties);

        $this->call_properties = $properties;

        return $this;
    }

    /**
     * @param CI_DB_query_builder $query
     * @param int $limit
     * @param int $offset
     */
    public function get_result($query, $limit = 0, $offset = 0)
    {
        if ($limit) {
            $query = $query->get($this->table_name, (int)$limit, (int)$offset);
        } else {
            $query = $query->get($this->table_name);
        }

        $result = $query->custom_result_object($this->entity_name);

        $this->_result_post_process($result);

        return $result;
    }

    /**
     * @param CI_DB_query_builder $query
     * @param int $limit
     * @param int $offset
     */
    public function get_result_one($query, $limit = 1)
    {
        $result = $this->get_result($query, $limit);
        return $result[0] ?: null;
    }

    /**
     * @param $result
     */
    protected function _result_post_process(&$result)
    {
        if (!empty($this->call_properties)) {
            foreach ($result as &$r) {
                if (method_exists($r, 'with_properties')) {
                    $r->with_properties($this->call_properties);
                }
            }
        }
    }

    /**
     * @param array $where
     * @return array|int
     */
    public function get_count($where = array())
    {
        $where = $this->_where_filter_site_seq($where);

        return $this->db
            ->where($where)
            ->get($this->table_name)
            ->num_rows();
    }

    /**
     * @param CI_DB_query_builder $query
     * @return int
     */
    function count($query)
    {
        return $query->from($this->table_name)->count_all_results();
    }

    /**
     * @param Entity_base|stdClass|array $entity
     * @return Entity_base|stdClass
     */
    public function insert($entity)
    {
        $now = getNow();

        if (is_array($entity)) {
            $entity = (object) $entity;
        } else {
            if (method_exists($entity, 'get_allowed_properties')) {

                $allowed_properties = $entity->get_allowed_properties();
                /**
                 * allowed_properties 에 있는 key 들은 DB Columns 가 아니므로 insert 에서 제외함
                 */
                if (!empty($allowed_properties)) {

                    $_entity = new stdClass();
                    foreach (get_object_vars($entity) as $key => $value) {
                        if (in_array($key, $allowed_properties)) {
                            continue;
                        }
                        $_entity->$key = $value;
                    }
                    $entity = $_entity;
                }
            }
        }

        $entity->created_at = $now;
        $entity->updated_at = $now;
        $entity->site_seq = SITE_SEQ;
        $this->db->insert($this->table_name, $entity);
        return $this->get_by_seq($this->db->insert_id());
    }

    /**
     * @param array $where
     * @return object
     */
    public function update($entity)
    {
        $now = getNow();

        if (is_array($entity)) {
            $entity = (object) $entity;
        } else {
            if (method_exists($entity, 'get_allowed_properties')) {

                $allowed_properties = $entity->get_allowed_properties();
                /**
                 * allowed_properties 에 있는 key 들은 DB Columns 가 아니므로 insert 에서 제외함
                 */
                if (!empty($allowed_properties)) {

                    $_entity = new stdClass();
                    foreach (get_object_vars($entity) as $key => $value) {
                        if (in_array($key, $allowed_properties)) {
                            continue;
                        }
                        $_entity->$key = $value;
                    }
                    $entity = $_entity;
                }
            }
        }

        $entity->updated_at = $now;
        $entity->site_seq = SITE_SEQ;

        $this->db->where('seq', $entity->seq);
        $this->db->update($this->table_name, $entity);

        return $this->get_by_seq($entity->seq);
    }

    /**
     * @param $seq
     * @return object
     */
    public function get_by_seq($seq)
    {
        $where = array();
        $where['site_seq'] = SITE_SEQ;
        $where['seq'] = $seq;

        return $this->get_one($where);
    }

    /**
     * 모델 key value HTML 테이블 렌더링
     */
    public function render_as_table()
    {
        $vars = get_object_vars($this);

        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>TYPE</th>";
        echo "<th>KEY</th>";
        echo "<th>VALUE</th>";
        echo "</tr>";

        foreach ($vars as $key => $value) {
            $type = gettype($value);
            echo "<tr>";
            echo "<td>{$type}</td>";
            echo "<td>{$key}</td>";
            echo "<td>{$value}</td>";
            echo "</tr>";
        }


        echo "</table>";
    }

    public function get_query_options($options)
    {
        if (isset($options['search_keyword'])) {
            $this->db->where($options['search_keyword'], NULL, FALSE);
        }

        if (isset($options['or_where_and'])) {
            $this->db->where($options['or_where_and'], NULL, FALSE);
        }

        if (!isset($options['search'])) $options['search'] = array();

        $query = $this->db->where($options['search']);

        if (!isset($options['or_where'])) $options['or_where'] = array();

        foreach ($options['or_where'] as $column_name => $column_values) {
            foreach ($column_values as $column_value) {
                $query = $this->db->or_where($column_name, $column_value);
            }
        }

        if (isset($options['like'])) {
            $query = $query->like($options['like']);
        }

        if (isset($options['like_none'])) {
            $query = $query->like($options['like_none'], 'none');
        }

        if (isset($options['or_like'])) {
            $query = $query->or_like($options['or_like']);
        }

        if (isset($options['in'])) {
            foreach ($options['in'] as $field => $values)
                $query = $query->where_in($field, $values);
        }

        if (isset($options['not_in'])) {
            foreach ($options['not_in'] as $field => $values)
                $query = $query->where_not_in($field, $values);
        }

        if (isset($options['order_by'])) {
            $query = $query->order_by($options['order_by']);
        }

        if (isset($options['select_max'])) {
            $query = $query->select_max($options['select_max']);
        }

        if (isset($options['group_by'])) {
            $query = $query->group_by($options['group_by']);
        }

        if (isset($options['having'])) {
            $query = $query->having($options['having']);
        }

        if (isset($options['select'])) {
            $query = $query->select($options['select']);
        }

        if (!isset($options['offset'])) $options['offset'] = 0;

        return $query;
    }

    /**
     * key: seq / value: [입력값] 를 갖는 array 로 결과물 trim
     * @param array $results
     * @param string $value_key
     * @param bool $seq_as_string
     * @return array
     */
    public function trim_seq_array($results, $value_key, $seq_as_string = false)
    {
        $array = array();

        foreach ($results as &$result) {
            if ($seq_as_string)
                $array[(string)$result->seq] = $result->$value_key;
            else
                $array[$result->seq] = $result->$value_key;
        }

        return $array;
    }

    /**
     * @param $results
     * @return array
     */
    public function get_key_row_array(&$results, $key = 'seq')
    {
        $array = array();

        foreach ($results as &$result) {
            $array[$result->$key] = $result;
        }

        return $array;
    }

    /**
     * @param array $results
     * @param string $key
     */
    public function get_value_array($results, $key, $is_unique = false)
    {
        $array = array();

        foreach ($results as $result) {
            $array []= $result->$key;
        }

        if ($is_unique)
            $array = array_unique($array);

        return $array;
    }

    /**
     * @param $results
     * @param $key
     */
    public function get_key_grouped_array($results, $key)
    {
        $array = array();

        foreach ($results as $i => &$result) {
            if (!$array[$result->$key]) {
                $array[$result->$key] = array();
            }
            $array[$result->$key] []= $result;
        }

        return $array;
    }

    public function search($properties = array())
    {
        $this->load->helper('query');

        $dummy_entity = new $this->entity_name();
        $queries = array();
        foreach ($properties as $property => $options) {
            if (!property_exists($dummy_entity, $property)) {
                continue;
            }

            if (isset($options['range'])) {
                $property .= (' ' . \Query_helper::convertRangeString($options['range']));
            }

            if (isset($options['in'])) {
                $queries['in'][$property] = is_array($options['keyword']) ? $options['keyword'] : array($options['keyword']);
            }

            if (isset($options['not_in'])) {
                $queries['in'][$property] = is_array($options['keyword']) ? $options['keyword'] : array($options['keyword']);
            }

            if (isset($options['like'])) {
                $queries['in'][$property] = is_array($options['keyword']) ? $options['keyword'] : array($options['keyword']);
            }

            $queries['search'][$property] = $options['keyword'];
        }
    }
}
