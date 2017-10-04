<?php

namespace classes;

use interfaces\Data;

/**
 * Vertex
 */
class Vertex
{
    const WORDS_DELIMITER = ' -> ';

    private $allVertexes = [];
    private $startVertex;
    private $endVertex;
    private $data;
    private $queue = [];

    /**
     * Init base properties
     * @param string $start
     * @param string $end
     * @param Data $data
     */
    public function __construct($start, $end, Data $data)
    {
        $this->startVertex = $start;
        $this->endVertex = $end;
        $this->data = $data;
    }

    /**
     * Find all words (vertexes) with a difference of 1 character
     * @param string $vertex
     * @return array|bool
     */
    private function findAll($vertex)
    {
        $result = [];
        $words = $this->data->getData();
        // тут вероятно не лучшее решение
        $regx = [
            '.' . mb_substr($vertex, 1),
            mb_substr($vertex, 0, 1) . '.' .  mb_substr($vertex, 2),
            mb_substr($vertex, 0, 2) . '.' .  mb_substr($vertex, 3),
            mb_substr($vertex, 0, 3) . '.',
        ];
        $regx = '/(*UTF8)(' . implode(')|(', $regx) . ')/';
        foreach ($words as $word) {
            if ($word == $vertex) {
                continue;
            }
            preg_match($regx, $word, $matches);
            if ($matches) {
                if ($word == $this->endVertex) {
                    return true;
                }
                $result[] = $word;
            }
        }
        return $result ? : false;
    }

    /**
     * Remove not unique words
     * @param array $vertexes
     * @return array|bool
     */
    private function clearVertex($vertexes)
    {
        $vertexes = array_diff($vertexes, $this->allVertexes);
        if ($vertexes) {
            $this->allVertexes = array_merge($this->allVertexes, $vertexes);
        }
        return $vertexes ? array_flip($vertexes) : false;
    }

    /**
     * Find all unique words with a difference of 1 character
     * @param string $vertex
     * @return array|bool
     */
    private function find($vertex)
    {
        $all = $this->findAll($vertex);
        if (is_bool($all)) {
            return $all;
        }
        return $this->clearVertex($all);
    }

    /**
     * From flyweight to elephant
     * Search in width
     */
    public function run()
    {
        $this->queue[$this->startVertex] = $this->find($this->startVertex);
        while (!empty($this->queue)) {
            $graph = current($this->queue);
            $vertexPath = key($this->queue);
            $path = $this->readGraph($graph, $vertexPath);
            if ($path) {
                return $path . self::WORDS_DELIMITER . $this->endVertex;
            }
        }
        throw new \Exception('Result not exist');
    }

    /**
     * Read simple graph (one output vertex) & create a queue
     * @param array $graph
     * @param string $vertexPath
     * @return string|bool
     */
    private function readGraph($graph, $vertexPath)
    {
        if (is_array($graph)) {
            foreach ($graph as $vertex => $value) {
                $value = $this->find($vertex);
                unset($this->queue[$vertexPath]);
                $this->queue[$vertexPath . self::WORDS_DELIMITER . $vertex] = $value;
            }
        }
        elseif ($graph === true) {
            return $vertexPath;
        }
        elseif ($graph === false) {
            unset($this->queue[$vertexPath]);
        }
        return false;
    }
}